<?php

namespace App\Http\Controllers\Compra;

use App\Http\Controllers\Controller;
use App\Models\armagen;
use App\Models\movement_type;
use App\Models\movement_type_produtos;
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
        return Inertia::render('purchase');
    }

    public function getPurchases(Puchase $puchase)
    {
        return $puchase->orderBy('id','desc')->get();
    }

    public function Order(Puchase $order)
    {
       
        $data = $order->with(['items' => function ($items) {
            $items->orderBy('id', 'ASC');
        }])->whereId($order->id)->first();
        $data->load('payments');
        return $data;
    }

    public function checkOrder($order)
    {
        if (Puchase::find($order)->state != 'Cotação') return false;
        return true;
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
            'user_id' => Auth()->user()->id
        ]);

       return $this->Order($data);
    }

    public function AddItemPuchase(Request $request, $order)
    {
        if (!$this->checkOrder($order)) return $this->RespondInfo('Esta encomenda ja foi confirmada');

        $result = PuchaseItem::where('puchase_id', $order)->where('produtos_id', $request->id)->exists();
        if ($result)
        return $this->RespondError('Este produto já foi Adicionada nessa encomenda',[]);

        PuchaseItem::create([
            'puchase_id' => $order,
            'produtos_id' => $request->id,
            'priceCost' => $request->preçocust,
            'totalItem' => $request->preçocust,
            'finalPrice' => $request->preçocust,
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
        $item->tax = $update['tax'];
        $item->discount = $update['discount'];
        $item->save();
        return $this->SumPuchase($update['puchase_id']);
    }

    public function deleteItem($order,PuchaseItem $item)
    {
        if (!$this->checkOrder($order)) return $this->RespondInfo('Esta encomenda ja foi confirmada');
        $item->delete();
        return $this->SumPuchase($order);
    }

    public function confirmOrder(Request $request,Puchase $order,armagen $armagen)
    {

        if (!$this->checkOrder($order->id)) return $this->RespondError('Atenção esta encomenda ja foi confirmada ');
        $order->load('items');
        DB::transaction(function() use ($order, &$armagen, &$request){
            foreach ($order->items as $item) {

                $consult = stock::where('produtos_id', $item['produtos_id'])
                ->where('armagen_id', $armagen->id);

                if ($consult->count() > 0) {
                    $quantityAfter = $consult->first()->quantity;
                    $consult->update([
                        'quantity' => $consult->first()->quantity + $item['quantity']
                    ]);
                } else {
                    $quantityAfter = 0;
                    $consult->create([
                        'produtos_id' => $item['produtos_id'],
                        'armagen_id' => $armagen->id,
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
                    'armagen_id' => $armagen->id,
                    'quantity' => $item['quantity'],
                    'price_cost' => $item['finalPrice'],
                    'motive' => "Compra",
                    'quantityAfter' => $quantityAfter,
                ]);
                $order->fornecedor_id = $request->supplier['id'];
                $order->state = 'Publicado';
                $order->restPayable = $order->total;
                $order->armagen_id = $armagen->id;
                $order->save();
            }
        });

        return $this->Order($order);
    }

    public function savePayment(Request $request,Puchase $order)
    {
        $request->validate([
            'valor' => 'required|integer',
        ]);

        $data = $request->all();

        $RestPayable = $order->restPayable - $data['valor'];
        if($RestPayable <= 0) {
            $RestPayable = 0;
            $order->state = "Pago";
        }
        $order->restPayable = $RestPayable;
        $order->save();
        $data['TotalPayments'] = $RestPayable;
        $order->payments()->create([
            'puchase_id' => $order->id,
            'payment_method_id' => $data['id'],
            'Amount' => $data['valor'],
            'TotalPayments' => $RestPayable,
        ]);

        return $this->RespondSuccess('Pagamento efectuado com sucesso',$this->Order($order));
    }
}
