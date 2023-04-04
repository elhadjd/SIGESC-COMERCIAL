<?php

namespace App\Http\Controllers\Compra;

use App\Http\Controllers\Controller;
use App\Models\Puchase;
use App\Models\PuchaseItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class compraController extends Controller
{
    public function index()
    {
        return Inertia::render('purchase');
    }

    public function getPurchases()
    {
        return Puchase::all();
    }

    public function Order(Puchase $order)
    {
        return $order->load('items');
    }

    public function SumPuchase($order)
    {
        $order = Puchase::find($order);

        $orderPuchase = Puchase::withSum('items','spent')->withSum('items','totalDiscount')
        ->withSum('items','totalTax')
        ->withSum('items','totalItem')
        ->first();

        $order->totalDiscount = $orderPuchase->items_sum_total_discount;
        $order->totalTax = $orderPuchase->items_sum_total_tax;
        $order->TotalSpending = $orderPuchase->items_sum_spent;
        $order->totalMerchandise = $orderPuchase->items_sum_total_item - $orderPuchase->items_sum_total_tax + $orderPuchase->items_sum_total_discount - $orderPuchase->items_sum_spent;
        $order->total = $orderPuchase->items_sum_total_item;
        $order->save();

        return $order->load('items');
    }

    public function AddItemPuchase(Request $request, $order)
    {

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
    {
        $update = $request;
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
}
