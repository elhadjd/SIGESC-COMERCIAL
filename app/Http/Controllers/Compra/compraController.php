<?php

namespace App\Http\Controllers\Compra;

use App\Http\Controllers\Controller;
use App\Models\armagen;
use App\Models\movement_type;
use App\Models\movement_type_produtos;
use App\Models\paymentMethod;
use App\Models\produtos;
use App\Models\Puchase;
use App\Models\PuchaseItem;
use App\Models\stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class compraController extends Controller
{
    public function index()
    {
        return Inertia::render('Purchase/index');
    }

    public function getPurchases(Puchase $puchase)
    {
        return $puchase->where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(50);
    }

    public function Order(Puchase $order)
    {

        $data = $order->with(['items' => function ($items) {
            $items->orderBy('id', 'desc');
        }])->whereId($order->id)->first();
        $data->load('payments');
        return $data;
    }

    public function checkOrder($order)
    {
        if (Puchase::find($order)->state != 'Cotação') return false;
        return true;
    }

    public function ChangeDatePurchase(Request $request,$type, Puchase $order)
    {
        $order[$type] = $request[$type];
        $order->save();
    }

    public function SumPuchase($order)
    {
        $order = Puchase::find($order);

        $orderPuchase = Puchase::withSum('items','spent')->withSum('items','totalDiscount')
        ->withSum('items','totalTax')
        ->withSum('items','totalItem')
        ->where('id',$order->id)
        ->first();

        if ($orderPuchase->items_sum_total_item == null) {
            $orderPuchase->items_sum_total_discount = 0;
            $orderPuchase->items_sum_total_tax = 0;
            $orderPuchase->items_sum_spent = 0;
            $orderPuchase->items_sum_total_item = 0;
        }

        $order->totalDiscount = $orderPuchase->items_sum_total_discount;
        $order->totalTax = $orderPuchase->items_sum_total_tax;
        $order->TotalSpending = $orderPuchase->items_sum_spent;
        $order->totalMerchandise = $orderPuchase->items_sum_total_item - $orderPuchase->items_sum_total_tax + $orderPuchase->items_sum_total_discount - $orderPuchase->items_sum_spent;
        $order->total = $orderPuchase->items_sum_total_item;
        $order->save();

        return $this->Order($order);
    }

    public function NewPurchase(Puchase $puchase)
    {
        $data = $puchase->create([
            'company_id'=>Auth::user()->company_id,
            'user_id' => Auth()->user()->id
        ]);

        $date = date('d').'-'.date('m').'-'.date('Y');
        $data->orderNumber = 'CP'.$date.'/'.$data->id;
        $data->save();

       return $this->Order($data);
    }

    public function addSupplier(Puchase $order,$supplier)
    {

        $order->fornecedor_id = $supplier;
        $order->save();

        return $order->fresh()->supplier;
    }

    public function AddItemPuchase(produtos $product, $order)
    {
        if (!$this->checkOrder($order)) return $this->RespondInfo('Esta encomenda ja foi confirmada');

        $result = PuchaseItem::where('puchase_id', $order)->where('produtos_id', $product->id)->exists();
        if ($result)
        return $this->RespondError('Este produto já foi Adicionada nessa encomenda',[]);

        $puchase = PuchaseItem::create([
            'quantity' => 1,
            'puchase_id' => $order,
            'produtos_id' => $product->id,
            'priceCost' => $product->preçocust,
            'totalItem' => $product->preçocust,
            'finalPrice' => $product->preçocust,
            'armagen_id' => Auth::user()->armagen_id
        ]);

        return $this->SumPuchase($order);
    }

    public function UpdateItems(Request $request,PuchaseItem $item)
    {   $update = $request;

        if (!$this->checkOrder($update['puchase_id'])) return $this->RespondInfo('Esta encomenda ja foi confirmada');
        $totalIva = ceil($request->priceCost/100 * $request->tax * $request->quantity);
        $totalDiscount = ceil($request->priceCost/100 * $request->discount * $request->quantity);
        $item->totalTax = $totalIva;
        $item->totalDiscount = $totalDiscount;
        $sum = $request->quantity * $request->priceCost - $totalDiscount + $totalIva;
        $item->finalPrice = $sum / $update['quantity'] + $update['spent'] / $update['quantity'];
        $item->totalItem = $item->finalPrice * $update['quantity'];
        $item->spent = $update['spent'];
        $item->quantity = $update['quantity'];
        $item->priceCost = $update['priceCost'];
        $item->tax = $update['tax'];
        $item->discount = $update['discount'];
        $item->armagen_id = $update['armagen_id'];
        $item->save();
        return $this->SumPuchase($update['puchase_id']);
    }

    public function deleteItem($order,PuchaseItem $item)
    {
        if (!$this->checkOrder($order)) return $this->RespondInfo('Esta encomenda ja foi confirmada');
        $item->delete();
        return $this->SumPuchase($order);
    }

    public function confirmOrder(Request $request,Puchase $order,$type)
    {

        if (!$this->checkOrder($order->id) && $type != 'cancel') return $this->RespondError('Atenção esta encomenda ja foi confirmada ',$order);
        $order->load('items');

        if ($order->state == 'Anulado' && $type == 'cancel') return $this->RespondInfo('Esta compra ja se encontra anulada ',$order);

        if (empty($order->fornecedor_id)) return $this->RespondError('Seleciona um fornecedor para validar a compra',$order);

        DB::transaction(function() use ($order, &$request,&$type){
            foreach ($order->items as $item) {
                $consult = stock::where('produtos_id', $item['produtos_id'])
                ->where('armagen_id', $item['armagen_id']);

                if ($consult->count() > 0) {
                    $quantityAfter = $consult->first()->quantity;
                    $consult->update([
                        'quantity' => $type == 'save' ? $consult->first()->quantity + $item['quantity'] : $consult->first()->quantity - $item['quantity']
                    ]);
                } else {
                    $quantityAfter = 0;
                    $consult->create([
                        'produtos_id' => $item['produtos_id'],
                        'armagen_id' => $item['armagen_id'],
                        'quantity' => $item['quantity']
                    ]);
                }

                produtos::find($item['produtos_id'])->update([
                    'preçocust' => $item['finalPrice']
                ]);

                $movementTypes = movement_type::all()->where('name', 'Compra')->first();

                movement_type_produtos::create([
                    'user_id' => $request->user()->id,
                    'produtos_id' => $item->product['id'],
                    'movement_type_id' => $movementTypes->id,
                    'armagen_id' => $item['armagen_id'],
                    'quantity' => $item['quantity'],
                    'price_cost' => $item['finalPrice'],
                    'motive' => $type == 'save' ? "Compra Confirmada" : 'Compra Anulada',
                    'quantityAfter' => $quantityAfter,
                ]);

                $order->state = $type == 'save' ? 'Publicado' : 'Anulado';
                $order->restPayable = $order->total;
                $order->armagen_id = $item['armagen_id'];
                $order->save();
            }
        });

        return $this->Order($order);
    }

    public function savePayment(Request $request,Puchase $order)
    {
        $request->validate([
            'Amount' => 'required|integer',
        ]);

        $data = $request->all();
        DB::transaction(function()use($order,&$data){
            $RestPayable = $order->restPayable - $data['Amount'];
            if($RestPayable <= 0) {
                $RestPayable = 0;
                $order->state = "Pago";
            }
            $order->restPayable = $RestPayable;
            $order->save();
            $data['TotalPayments'] = $RestPayable;
            $order->payments()->create([
                'puchase_id' => $order->id,
                'payment_method_id' => $data['payment_method_id'],
                'Amount' => $data['Amount'],
                'TotalPayments' => $RestPayable,
            ]);
        });



        return $this->RespondSuccess('Pagamento efectuado com sucesso',$this->Order($order));
    }

    public function getPayments(paymentMethod $paymentMethod)
    {
        return $paymentMethod->with(['paymentsPurchases'=>function($payments)
        {
            $payments->with('purchase');
        }])->get();
    }

    public function payments(Puchase $order)
    {
        return $order->payments()->with('method')->get();
    }
}
