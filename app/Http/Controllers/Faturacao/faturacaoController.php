<?php

namespace App\Http\Controllers\Faturacao;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\movement_type;
use App\Models\movement_type_produtos;
use App\Models\PaymentInvoice;
use App\Models\produtos;
use Faker\Core\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class faturacaoController extends Controller
{
    public function index()
    {
        return Inertia::render('Invoice');
    }

    public function getInvoices($invoice = null)
    {
        if (!$invoice) return Invoice::orderBy('id','desc')->get();
        $select = Invoice::with(['items' => function ($items) {
            $items->orderBy('id', 'asc');
        }]);
        return $select->where('id',$invoice)->first();
    }

    public function NewOrder(Invoice $invoice)
    {
        $orderNumber = "FT".date('d-m-Y')."/";
        $create = $invoice->create([
            'orderNumber'=> $orderNumber,
            'user_id' => Auth()->user()->id
        ]);

        return $this->getInvoices($create->id);
    }

    public function checkQuantity(produtos $product, $quantity = null)
    {
        $checkQuantity = $product->withSum('stock', 'quantity')->where('id', $product->id)->first();
        if ($quantity != null ? $checkQuantity->stock_sum_quantity < $quantity : $checkQuantity->stock_sum_quantity <= 0) return false;
        return true;
    }

    public function AddItem(produtos $product, $invoice)
    {
        if (!$this->checkQuantity($product)) return $this->RespondError('Este produto não tem quantidade suficiente em stock');
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

        return $this->sumOrder($invoice);
    }

    public function UpdateRows(Request $request, $invoice)
    {
        $dados = $request->dados;
        if (!$this->checkQuantity(produtos::find($dados['produtos_id']), $dados['quantity'])) return $this->RespondError('Este produto não tem quantidade suficiente em stock');
        $TotalDescont = ceil($dados['PriceSold'] / 100 * $dados['Discount'] * $dados['quantity']);
        $total = $dados['PriceSold'] * $dados['quantity'];
        $dados['TotalDiscount'] = $TotalDescont;
        $dados['TotalSold'] = $total - $TotalDescont;
        $item = InvoiceItem::find($dados['id']);
        unset($dados['product'], $dados['id'], $dados['updated_at'], $dados['created_at']);
        if ($item->update($dados)) {
            return $this->sumOrder($invoice);
        } else {
            return $this->message('Atenção aconteçeu um erro no sistema por favor atualize seu navigador e tenta novamente !!!', 'error');
        }
    }

    public function deleteItem($invoice, InvoiceItem $item)
    {
        $item->delete();
        return $this->sumOrder($invoice);
    }

    public function sumOrder($invoice)
    {
        $order = Invoice::withSum('items', 'TotalSold')->withSum('items', 'TotalDiscount')->where('id',$invoice)->first();
        if ($order->items_sum_total_sold == null) {
            $order->items_sum_total_discount = 0;
            $order->items_sum_total_sold = 0;
        };
        $invoice = Invoice::find($invoice);
        $invoice->discount = $order->items_sum_total_discount;
        $invoice->TotalMerchandise = $order->items_sum_total_sold + $order->items_sum_total_discount;
        $invoice->TotalInvoice = $order->items_sum_total_sold;
        $invoice->save();
        return $this->getInvoices($invoice->id);
    }

    public function ChangeDateInvoice(Request $request, Invoice $invoice)
    {
        $invoice->DateOrder = $request->DateOrder;
        $invoice->DateDue = $request->DateDue;
        $invoice->save();
    }

    public function ConfirmOrder(Request $request, Invoice $invoice)
    {
        $stock_insuficient = false;

        $items = $invoice->load('items');

        DB::transaction(function () use ($request, &$invoice, &$items, &$stock_insuficient) {
            foreach ($items->items as $item) {
                $stock = $request->user()->armagen()
                ->first()->stock()->where('produtos_id', $item['produtos_id'])
                ->first();
                if ($stock->quantity < $item['quantity']) {
                    return $stock_insuficient = true;
                } else {
                    $quantityAfter = $stock->quantity;

                    $quantity = $stock->quantity - $item['quantity'];
                    $stock->quantity = $quantity;
                    $stock->save();

                    $movementTypes = movement_type::all()->where('name', 'Vendido por Faturação')->first();

                    movement_type_produtos::create([
                        'user_id' => $request->user()->id,
                        'produtos_id' => $item->product['id'],
                        'movement_type_id' => $movementTypes->id,
                        'armagen_id' => $stock->armagen_id,
                        'quantity' => $item['quantidade'],
                        'price_cost' => $item->product['preçocust'],
                        'price_sold' => $item['priceSold'],
                        'motive' => "Faturação",
                        'quantityAfter' => $quantityAfter,
                    ]);

                    $invoice->state = 'Publicado';
                    $invoice->RestPayable = $invoice->TotalInvoice;
                    $invoice->save();
                }
            }
        });

        if ($stock_insuficient) return $this->RespondError('Stock do produto insuficiente', []);

        return $this->getInvoices($invoice->id);
    }

    public function InvoicePayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_method_id' => 'required|integer',
            'Amount' => 'required|integer',
        ]);

        $data = $request->all();

        $RestPayable = $invoice['RestPayable'] - $data['Amount'];
        if($RestPayable <= 0) {
            $RestPayable = 0;
            $invoice->state = "Pago";
        }
        $invoice->RestPayable = $RestPayable;
        $invoice->save();
        $data['TotalPayments'] = $RestPayable;
        $invoice->payments()->create($data);

        return $this->RespondSuccess('Pagamento efectuado com sucesso',$this->getInvoices($invoice->id));
    }
}
