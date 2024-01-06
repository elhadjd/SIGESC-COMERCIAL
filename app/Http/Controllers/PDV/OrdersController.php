<?php

namespace App\Http\Controllers\PDV;

use App\Http\Controllers\Controller;
use App\Models\ItemOrder;
use App\Models\movement_type;
use App\Models\movement_type_produtos;
use App\Models\orderPos;
use App\Models\paymentPDV;
use App\Models\produtos;
use App\Models\session;
use App\Models\stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    public function ValidatePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => [
                'required',
                Rule::unique('order_pos')
            ],
        ]);

        if($validator->fails()){
            $order = orderPos::where('number',$request->number)->first();
            return $this->returnInvoice($order);
        }else{
            $listItems = $request->items;
            $methods = $request->methods;
            $session = session::find($request->session);

            if ($session->state != "Aberto") return $this->RespondSuccess(__('Error, the session for this point of sale is closing'));
            if ($listItems != null) {

                $VerificarPagamento = $this->VerificarValorPago($methods, $listItems);

                if ($VerificarPagamento['total'] <= $VerificarPagamento['ValorPago']) {

                    $message = [
                        'state'=> false,
                        'message'=>null
                    ];
                    $idOrder = 0;
                    DB::transaction(function () use ($request, &$methods, &$idOrder, &$VerificarPagamento, &$message) {
                        foreach ($request->items as $item) {
                            $stock = $request->user()->armagen()->first()
                                ->stock()->where('produtos_id', $item['id']);
                            if ($stock->count() <= 0) {
                                $message['state'] = true;
                                $message['message'] = 'O produto '.$item['nome'].'não existe no armagen relacionado';
                            }elseif($stock->first()->quantity < $item['quantidade']){
                                $message['state'] = true;
                                $message['message'] = 'O produto '.$item['nome'].' não tem quantidade suficiente em stock';
                            }
                        }

                        if (!$message['state']) {
                            $order = orderPos::create([
                                'company_id' => Auth()->user()->company_id,
                                'session_id' => $request->session,
                                'user_id' => Auth()->user()->id,
                                'total' => $request->total,
                                'cliente' => $request->cliente,
                                'state' => 'Pago',
                                'number' => $request->number,
                                'total_costs' => $VerificarPagamento['total_costs']
                            ]);

                            foreach ($request->items as $item) {
                                $stock = $request->user()->armagen()->first()
                                    ->stock()->where('produtos_id', $item['id'])->first();
                                $product = produtos::find($item['id']);

                                $totals = $item['preco_pedido'] * $item['quantidade'];
                                $preco = $item['preco_pedido'];
                                $totalCusto = $product->preçocust * $item['quantidade'];
                                ItemOrder::create([
                                    'order_id' => $order->id,
                                    'produtos_id' => $product->id,
                                    'price_sold' => $preco,
                                    'price_cost' => $product->preçocust,
                                    'TotalCost' => $totalCusto,
                                    'quantity' => $item['quantidade'],
                                    'total' => $totals,
                                ]);

                                $quantityAfter = $stock->quantity;

                                $quantity = $stock->quantity - $item['quantidade'];
                                $stock->quantity = $quantity;
                                $stock->save();

                                $movementTypes = movement_type::all()->where('name', 'Vendido por PDV')->first();

                                movement_type_produtos::create([
                                    'company_id' => Auth::user()->company_id,
                                    'user_id' => Auth::user()->id,
                                    'produtos_id' => $product->id,
                                    'order_pos_id' => $order->id,
                                    'movement_type_id' => $movementTypes->id,
                                    'armagen_id' => $stock->armagen_id,
                                    'quantity' => $item['quantidade'],
                                    'price_cost' => $product->preçocust,
                                    'price_sold' => $preco,
                                    'motive' => "Venda",
                                    'quantityAfter' => $quantityAfter,
                                ]);
                            }
                            foreach ($methods as $method) {
                                if ($method['valor'] > 0) {
                                    paymentPDV::create([
                                        'session_id' => $request->session,
                                        'order_pos_id' => $order->id,
                                        'payment_method_id' => $method['id'],
                                        'amountPaid' => $method['valor'],
                                        'change' => $method['name'] == 'Numerario' ? $VerificarPagamento['ValorPago'] - $request->total : 0,
                                    ]);
                                }
                            }

                            $idOrder = $order->id;
                        }
                    });

                    if ($message['state']) return $this->RespondError($message['message'], []);

                    if ($idOrder > 0)  return $this->Invoice($idOrder);
                } else {
                    return $this->RespondError(__('Insufficient value'));
                }
            } else {
                return $this->RespondError(__('Error occurs when updating data'));
            }
        }
    }

    public function VerificarEncomenda($order)
    {
        return orderPos::where('number', "$order->session-$order->number")
            ->where('session_id', $order->session)
            ->where('total', $order->total)->first();
    }

    public function VerificarValorPago($metodos, $items)
    {
        $encomeda = [
            'totalEncomenda' => 0,
            'troco' => 0,
            'valorPago' => 0,
            'TotalCosts' => 0,
        ];
        foreach ($metodos as $metodo) {
            $encomeda['valorPago'] += $metodo['valor'];
        }
        foreach ($items as $pedido) {
            $cost = $pedido['quantidade'] * $pedido['preçocust'];
            $encomeda['TotalCosts'] += $cost;
            $encomeda['totalEncomenda'] += $pedido['total'];
        }
        $encomeda['troco'] = $encomeda['totalEncomenda'] - $encomeda['valorPago'];

        return [
            'ValorPago' => $encomeda['valorPago'],
            'total' => $encomeda['totalEncomenda'],
            'troco' => $encomeda['troco'],
            'total_costs' => $encomeda['TotalCosts']
        ];
    }

    public function checkInvoiceDupleDelete(Request $request) {
        $order = $this->VerificarEncomenda($request);
        if ($order) {
            foreach ($order->items as $item) {
                $stock = $request->user()->armagen()->first()
                    ->stock()->where('produtos_id', $item['produtos_id'])->first();
                $quantity = $item->quantity + $stock->quantity;
                $stock->quantity = $quantity;
                $stock->save();
            }
            movement_type_produtos::where('order_pos_id',$order->id)->delete();
            paymentPDV::where('order_pos_id',$order->id)->delete();
            ItemOrder::where('order_id',$order->id)->delete();
            return $order->delete();
        };
        return 'true';
    }

    public function printInvoice(session $session,$type)
    {
        if (!$session) return $this->RespondError(__('A server error occurred'));
        if ($type == 'last') {
            $order = $session->orders()->orderBy('id','DESC')->first();
            $order->print = +1;
            $order->save();
            return $this->returnInvoice($order);
        }

        if($type == null || $type == "") return $this->RespondError(__('The invoice number field cannot be empty'));

        $order = orderPos::where('number',$type)->first();
        if (!$order) return $this->RespondWarn(__('No orders found with this number, please check and try again'));
        $order->print = +1;
        $order->save();
        return $this->returnInvoice($order);
    }

    function returnInvoice($order)
    {
        return $order->with(['items' => function ($query) {
            $query->with(['product' => function ($product) {
                $product->withSum('stock', 'quantity');
            }]);
        }])->whereId($order->id)->first();
    }

    public function Invoice($order)
    {
        $order = orderPos::find($order);
        if ($order->print > 0) return $this->RespondInfo(__('Attention, this invoice has already been printed, please contact the system administrator'));
        $order->print = 1;
        $order->save();
        return $this->returnInvoice($order);
    }

    public function groupBy(orderPos $orderPos,$event, $column)
    {
        return $orderPos->where($column,$event)
        ->where('company_id',Auth::user()->company_id)->with('session')->orderBy('id', 'desc')->paginate(100);
    }

    public function CancelInvoice(orderPos $order)
    {
        if(!request()->user()->hasRole('Admin')) return $this->RespondError(__('User without access'));
        $user = User::find($order->user_id);
        DB::transaction(function () use ($order, &$user) {

            foreach ($order->items as $item) {
                $stock = $user->armagen()->first()->stock()
                    ->where('produtos_id', $item['produtos_id'])
                    ->first();
                $stock_real = stock::find($stock->id);

                $stock_real->quantity += $item['quantity'];
                $stock_real->save();
            }

            $order->update([
                'state' => 'Anulado'
            ]);
        });
        $locale = app()->getLocale();

        return $this->RespondSuccess(
            __('Order canceled successfully'),
            orderPos::with(['payments'=>function($payments) use ($locale){
                $payments->with(['method'=>function($method) use ($locale){
                    $method->with(['methodTranslate'=>function($methodTranslate) use ($locale){
                        $methodTranslate->where('local',$locale);
                    }]);
                }]);
            }])->with('session')->where('id',$order->id)->first()
        );
    }

    public function getOrders($locale,$order=null, $column=null)
    {
        if (!request()->user()->hasRole('Admin')) {
            return $this->RespondError(__("User without access"));
        } else {
            if ($column == 'TotalMaior') {
                $orders = orderPos::where('total', '>=', $order)
                ->where('company_id',Auth::user()->company_id)
                    ->orderBy('total', 'ASC')->with('session')
                    ->paginate(100);
            } elseif ($column == 'TotalMenor') {
                $orders = orderPos::where('total', '<=', $order)
                ->where('company_id',Auth::user()->company_id)
                    ->orderBy('total', 'DESC')->with('session')
                    ->paginate(100);
            } else {
                $orderPos = $order;
                $orders = $this->getAllOrders($locale,$orderPos, $column);
            }

            return $orders;
        }
    }

    public function getOrderSingleUser($locale,$caixa)
    {
        return orderPos::where('session_id', $caixa)
        ->with(['payments'=>function($payments) use ($locale){
            $payments->with(['method'=>function($method) use ($locale){
                $method->with(['methodTranslate'=>function($methodTranslate) use ($locale){
                    $methodTranslate->where('local',$locale);
                }]);
            }]);
        }])
        ->with('session')->orderBy('id', 'ASC')->paginate(500);
    }

    public function getAllOrders($locale,$order = null, $colun = null)
    {
        if ($order != '') {
            return orderPos::where($colun, 'LIKE', '%' . $order . '%')
            ->where('company_id',Auth::user()->company_id)
            ->with(['payments'=>function($payments) use ($locale){
                $payments->with(['method'=>function($method) use ($locale){
                    $method->with(['methodTranslate'=>function($methodTranslate) use ($locale){
                        $methodTranslate->where('local',$locale);
                    }]);
                }]);
            }])
            ->orderBy('id', 'DESC')->with('session')->paginate(100);
        } else {
            return orderPos::with('session')->orderBy('id', 'DESC')
            ->where('company_id',Auth::user()->company_id)
            ->with(['payments'=>function($payments) use ($locale){
                $payments->with(['method'=>function($method) use ($locale){
                    $method->with(['methodTranslate'=>function($methodTranslate) use ($locale){
                        $methodTranslate->where('local',$locale);
                    }]);
                }]);
            }])
            ->paginate(100);
        }
    }
}
