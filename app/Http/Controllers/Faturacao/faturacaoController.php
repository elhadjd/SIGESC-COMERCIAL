<?php

namespace App\Http\Controllers\Faturacao;

use App\classes\ActivityRegister;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\TransferController;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\movement_type;
use App\Models\movement_type_produtos;
use App\Models\PaymentInvoice;
use App\Models\produtos;
use App\Models\stock;
use Faker\Core\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class faturacaoController extends Controller
{
    public function index()
    {
        $register = new ActivityRegister;
        $register->Activity("Entrou no module faturação");
        return Inertia::render('Invoice/index');
    }

    public function getInvoices($invoice = null)
    {
        if (!$invoice) return $this->companyUser()->invoice()->orderBy('id','desc')->paginate(50);
        $select = $this->companyUser()->invoice()->with(['items' => function ($items) {
            $items->orderBy('id', 'desc');
        }]);
        return $select->where('id',$invoice)->first();
    }

    public function NewOrder(Invoice $invoice)
    {
        $orderNumber = "FT".date('d-m-Y')."/";

        $create = $this->companyUser()->invoice()->create([
            'orderNumber'=> $orderNumber,
            'DateOrder'=>now(),
            'user_id' => Auth::user()->id
        ]);

        $register = new ActivityRegister;
        $register->Activity("Criou uma fatura no module faturação");
        return $this->getInvoices($create->id);
    }

    public function addClient(Invoice $order,$client)
    {
        $order->cliente_id = $client;
        $order->save();
        return $order->fresh()->client;
    }

    public function checkQuantity(produtos $product, $quantity = null)
    {
        $checkQuantity = $product->withSum('stock' , 'quantity')->where('id', $product->id)->first();

        if ($quantity != null ? $checkQuantity->stock_sum_quantity < $quantity : $checkQuantity->stock_sum_quantity <= 0) return false;
        return true;
    }

    public function AddItem(produtos $product, $invoice)
    {
        if (!$this->checkQuantity($product)) return $this->RespondError('Este produto não tem quantidade suficiente em stock');
        $result  = InvoiceItem::where('invoice_id', $invoice)
            ->where('produtos_id', $product->id)->exists();

        if ($result) return $this->RespondError('Este produto já foi Adicionada nessa encomenda');

        InvoiceItem::create([
            'invoice_id' => $invoice,
            'produtos_id' => $product->id,
            'quantity' => 1,
            'PriceCost' => $product->preçocust,
            'PriceSold' => $product->preçovenda,
            'TotalCost' => $product->preçocust,
            'TotalSold' => $product->preçovenda,
            'armagen_id' => Auth::user()->armagen_id,
            'final_price' => $product->preçovenda
        ]);

        return $this->sumOrder($invoice);
    }


    public function UpdateRows(Request $request, $item,TransferController $TransferController)
    {
        $dados = $request->all();
        if ($TransferController->checkQuantityCancel($dados)['state']) return $this->RespondError($TransferController->checkQuantityCancel($dados)['message'],$this->getInvoices($dados['invoice_id']));
        $totalIva = $request->PriceCost/100 * $request->tax * $request->quantity;
        $totalDiscount = ceil($request->PriceSold/100 * $request->Discount * $request->quantity);
        $sum = $request->quantity * $request->PriceSold - $totalDiscount + $totalIva;
        $dados['final_price'] = $sum / $request['quantity'];
        $total = $dados['PriceSold'] * $dados['quantity'];
        $dados['totalTax'] = $totalIva;
        $dados['TotalDiscount'] = $totalDiscount;
        $dados['TotalSold'] = ceil($total - $totalDiscount + $totalIva);
        $dados['TotalCost'] = $dados['PriceCost'] * $dados['quantity'];

        $item = InvoiceItem::find($item);
        unset($dados['product'], $dados['id'], $dados['updated_at'], $dados['created_at']);
        if ($item->update($dados)) {
            return $this->sumOrder($dados['invoice_id']);
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
        $order = Invoice::withSum('items', 'TotalSold')->withSum('items', 'TotalDiscount')
        ->withSum('items', 'totalTax')
        ->where('id',$invoice)->first();
        if ($order->items_sum_total_sold == null) {
            $order->items_sum_total_discount = 0;
            $order->items_sum_total_sold = 0;
            $order->items_sum_total_tax = 0;
        };
        $invoice = Invoice::find($invoice);
        $sum = $order->items_sum_total_sold - $order->items_sum_total_tax + $order->items_sum_total_discount;
        $invoice->discount = $order->items_sum_total_discount;
        $invoice->TotalMerchandise = $sum;
        $invoice->TotalInvoice = $order->items_sum_total_sold;
        $invoice->tax = $order->items_sum_total_tax;
        $invoice->save();
        return $this->getInvoices($invoice->id);
    }

    public function ChangeDateInvoice(Request $request,$type, Invoice $invoice)
    {
        $invoice[$type] = $request[$type];
        $invoice->save();
    }

    public function ConfirmOrder(Request $request, Invoice $invoice,$type,TransferController $TransferController)
    {
        $info = [
            'state'=> false,
            'message'=>null,
            'data'=>[]
        ];

        $items = $invoice->load('items');
        if ($type == 'cancel' && $items->state == 'Anulado') return $this->RespondError('Esta fatura ja esta anulada',$items);
        if ($type == 'cancel' && $items->state == 'Cotação') return $this->RespondInfo('Não é posivel anular uma ordem não confirmado',$items);
        if ($items->items->count() <= 0) return $this->RespondInfo('É preciso adicionar no minimo um produto na fatura',$items);
        DB::transaction(function () use ($request, &$invoice, &$items,&$type, &$info,&$TransferController) {
            if ($type == 'save') {
                foreach ($items->items as $item) {
                    if ($TransferController->checkQuantityCancel($item)['state']){
                       $info['state'] = $TransferController->checkQuantityCancel($item)['state'];
                       $info['message'] = 'A quantidade do produto '.$item['product']['nome'].' não tem quantidade suficiente em stock';
                    }
                }
            }

            if (!$info['state'] || $type == 'cancel') {
                foreach ($items->items as $item) {

                    $stock = stock::where('armagen_id',$item['armagen_id'])->where('produtos_id',$item['produtos_id'])
                    ->first();
                    $quantityAfter = $stock->quantity;

                    $quantity = $type == 'cancel' ? $stock->quantity + $item['quantity'] : $stock->quantity - $item['quantity'];
                    $stock->quantity = $quantity;
                    $stock->save();

                    $movementTypes = movement_type::where('name', 'Vendido por Faturação')->first();

                    movement_type_produtos::create([
                        'company_id' => Auth::user()->company_id,
                        'user_id' => $request->user()->id,
                        'produtos_id' => $item->product['id'],
                        'movement_type_id' => $movementTypes->id,
                        'armagen_id' => $stock->armagen_id,
                        'quantity' => $item['quantity'],
                        'price_cost' => $item->product['preçocust'],
                        'price_sold' => $item['final_price'],
                        'motive' => $type == 'save' ? "Fatura Confirmada":'Fatura cancelada',
                        'quantityAfter' => $quantityAfter,
                    ]);

                    $invoice->state = $type == 'save' ? "Publicado":'Anulado';
                    $invoice->RestPayable = $invoice->TotalInvoice;
                    $invoice->save();
                }

            }
        });

        if ($info['state']) return $this->RespondError($info['message'] , $items);

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

    public function getPayments(Invoice $invoice)
    {
        return $invoice->payments()->with('method')->get();
    }
}
