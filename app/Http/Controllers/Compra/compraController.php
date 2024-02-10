<?php

namespace App\Http\Controllers\Compra;

use App\classes\ActivityRegister;
use App\classes\CheckData;
use App\Http\Controllers\Controller;
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
        $register = new ActivityRegister;
        $register->Activity("Entrou no module compra");
        return Inertia::render('Purchase/index');
    }

    public function getPurchases(Puchase $puchase)
    {
        return $puchase->where('company_id', Auth::user()->company_id)->orderBy('id', 'desc')->paginate(50);
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

    public function ChangeDatePurchase(Request $request, $type, Puchase $order)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Purchase','Edit')) return $this->RespondError(__('User without access'));
        $order[$type] = $request[$type];
        $order->save();
    }

    public function SumPuchase($order)
    {
        $order = Puchase::find($order);

        $orderPuchase = Puchase::withSum('items', 'spent')->withSum('items', 'totalDiscount')
            ->withSum('items', 'totalTax')
            ->withSum('items', 'totalItem')
            ->where('id', $order->id)
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
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Purchase','Create')) return $this->RespondError(__('User without access'));
        $data = $this->companyUser()->purchase()->create([
            'DateOrder'=>now(),
            'user_id' => Auth()->user()->id
        ]);

        $date = date('d') . '-' . date('m') . '-' . date('Y');
        $data->orderNumber = 'CP' . $date . '/' . $data->id;
        $data->save();

        return $this->Order($data);
    }

    public function addSupplier(Puchase $order, $supplier)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Purchase','Edit')) return $this->RespondError(__('User without access'));
        $order->fornecedor_id = $supplier;
        $order->save();
        return $order->fresh()->supplier;
    }

    public function AddItemPuchase(produtos $product, $order)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Purchase','Edit')) return $this->RespondError(__('User without access'));
        if (!$this->checkOrder($order)) return $this->RespondInfo(__('This order has already been confirmed.'));

        $result = PuchaseItem::where('puchase_id', $order)->where('produtos_id', $product->id)->exists();
        if ($result)
            return $this->RespondError(__('This product has already been added to this order.'), []);

        PuchaseItem::create([
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

    public function UpdateItems(Request $request, PuchaseItem $item)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Purchase','Edit')) return $this->RespondError(__('User without access'));
        
        $update = $request;

        if (!$this->checkOrder($update['puchase_id'])) return $this->RespondInfo(__('This order has already been confirmed.'));
        $totalIva = ceil($request->priceCost / 100 * $request->tax * $request->quantity);
        $totalDiscount = ceil($request->priceCost / 100 * $request->discount * $request->quantity);
        $item->totalTax = $totalIva;
        $item->totalDiscount = $totalDiscount;
        $sum = $request->quantity * $request->priceCost - $totalDiscount + $totalIva;
        $item->finalPrice = $sum / $update['quantity'] + $update['spent'] / $update['quantity'];
        $item->totalItem = $item->finalPrice * $update['quantity'];
        $item->spent = $update['spent'];
        $item->quantity = $update['quantity'];
        $item->priceCost = $request->priceCost;
        $item->tax = $update['tax'];
        $item->discount = $update['discount'];
        $item->armagen_id = $update['armagen_id'];
        $item->save();
        return $this->SumPuchase($update['puchase_id']);
    }

    public function deleteItem($order, PuchaseItem $item)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Purchase','Delete')) return $this->RespondError(__('User without access'));
        if (!$this->checkOrder($order)) return $this->RespondInfo(__('This order has already been confirmed.'));
        $item->delete();
        return $this->SumPuchase($order);
    }

    public function confirmOrder(Request $request, Puchase $order, $type)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Purchase','Edit')) return $this->RespondError(__('User without access'));
        if (!$this->checkOrder($order->id) && $type != 'cancel') return $this->RespondError(__('This order has already been confirmed.'), $order);
        $order->load('items');

        if ($order->state == 'Anulado' && $type == 'cancel') return $this->RespondInfo(__('This purchase has already been canceled.'), $order);

        if (empty($order->fornecedor_id)) return $this->RespondError(__('Select a supplier to validate the purchase.'), $order);

        DB::transaction(function () use ($order, &$request, &$type) {
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
                    'company_id' => Auth::user()->company_id,
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

    public function savePayment(Request $request, Puchase $order)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Payments','Create')) return $this->RespondError(__('User without access'));
        $request->validate([
            'Amount' => 'required|integer',
        ]);

        $data = $request->all();
        DB::transaction(function () use ($order, &$data) {
            $RestPayable = $order->restPayable - $data['Amount'];
            if ($RestPayable <= 0) {
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

        return $this->RespondSuccess(__('Payment Successful'), $this->Order($order));
    }

    public function getPayments(Request $request,paymentMethod $paymentMethod)
    {
        $locale = $request->user()->userLanguage->code ? $request->user()->userLanguage->code : 'en';
        return $paymentMethod->with(['paymentsPurchases' => function ($payments) {
            $payments->with('purchase');
        }])->with(['methodTranslate'=>function($translate) use ($locale){
            $translate->where('local',$locale);
        }])->get();
    }

    public function payments(Puchase $order)
    {
        return $order->payments()->with('method')->get();
    }
}
