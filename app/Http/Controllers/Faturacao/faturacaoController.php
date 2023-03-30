<?php

namespace App\Http\Controllers\Faturacao;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\produtos;
use Illuminate\Http\Request;
use Inertia\Inertia;

class faturacaoController extends Controller
{
    public function index()
    {
        return Inertia::render('Invoice');
    }

    public function getInvoices($invoice = null)
    {
        if (!$invoice) return Invoice::all();
        return Invoice::find($invoice)->load('items');
    }

    public function NewOrder()
    {
        Invoice::create([
            'user_id' => Auth()->user()->id
        ]);
    }

    public function AddItem(produtos $product, $invoice)
    {
        $result  = InvoiceItem::where('invoice_id', $invoice)
            ->where('produtos_id', $product->id)->exists();

        if ($result)
            return $this->RespondError('Este produto já foi Adicionada nessa encomenda');

        InvoiceItem::create([
            'invoice_id' => $invoice,
            'produtos_id' => $product->id,
            'quantity' => 1,
            'PriceCost' => $product->preçocust,
            'PriceSold' => $product->preçovenda,
            'TotalCost' => $product->preçocust,
            'TotalSold' => $product->preçovenda
        ]);

        return $this->getInvoices($invoice);
    }

    public function deleteItem($invoice, InvoiceItem $item)
    {
        $item->delete();
        return $this->sumOrder($invoice);
    }

    public function sumOrder($invoice)
    {
        $invoice = Invoice::find($invoice);
        $order = $invoice->withSum('items', 'TotalSold')
            ->withSum('items', 'TotalDiscount')->first();

        $invoice->TotalInvoice = $order->items_sum_total_sold;
        $invoice->TotalMerchandise = $order->items_sum_total_sold - $order->items_sum_total_discount;
        $invoice->save();
        return $this->getInvoices($invoice->id);
    }
}
