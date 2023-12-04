<?php

namespace App\Http\Controllers\PDV;

use App\Http\Controllers\Controller;
use App\Models\ItemOrder;
use App\Models\movement_type;
use App\Models\movement_type_produtos;
use App\Models\orderPos;
use App\Models\paymentMethod;
use App\Models\paymentPDV;
use App\Models\produtos;
use App\Models\session;
use App\Models\stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function ValidatePayment(Request $request)
    {
        $listItems = $request->items;
        $methods = $request->methods;
        $session = session::find($request->session);

        if ($session->state != "Aberto") return $this->RespondSuccess('Erro a sessão desta Caixa esta fechar ');
        if ($listItems != null) {

            $VerificarPagamento = $this->VerificarValorPago($methods, $listItems);

            if ($VerificarPagamento['total'] <= $VerificarPagamento['ValorPago']) {

                // $order = $this->VerificarEncomenda($request);

                // if ($order) return $this->Invoice($order->id);
                $message = [
                    'state'=> false,
                    'message'=>null
                ];
                // $stock_insuficiente = false;
                // $price_authorized = false;
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
                            'number' => "$request->session-$request->number",
                            'total_costs' => $VerificarPagamento['total_costs']
                        ]);

                        foreach ($request->items as $item) {
                            $stock = $request->user()->armagen()->first()
                                ->stock()->where('produtos_id', $item['id'])->first();
                            $product = produtos::find($item['id']);
                            // if ($product->preçovenda != $item['preco_pedido']) {

                            // if ($request->user()->config->price || count($product->list_price) > 0) {
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

                            //     } else {
                            //         return $price_authorized = true;
                            //     }
                            // }
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
                return $this->RespondError('Valor insuficiente');
            }
        } else {
            return $this->RespondError('Erro nesta encomenda');
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
        if (!$session) return $this->RespondError('Aconteceu um erro no sistema');
        if ($type == 'last') {
            $order = $session->orders()->orderBy('id','DESC')->first();
            $order->print = +1;
            $order->save();
            return $this->returnInvoice($order);
        }

        if($type == null || $type == "") return $this->RespondError('O campo numero de fatura não pode ser vazio');

        $order = orderPos::where('number',$type)->first();
        if (!$order) return $this->RespondWarn('Nenhuma fatura encontrada com este numero por favor verifique e tenta novamente');
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
        if ($order->print > 0) return $this->RespondInfo('Atenção esta fatura ja foi imprimida por favor contacte o administrador do sistema !!!');
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

        return $this->RespondSuccess(
            'Fatura Anulada com Sucesso',
            $order->load('session')
        );
    }

    public function getOrders($order=null, $column=null)
    {
        if (Auth::user()->nivel != 'admin') {
            return false;
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
                $orders = $this->getAllOrders($orderPos, $column);
            }

            return $orders;
        }
    }

    public function getOrderSingleUser($caixa)
    {
        return orderPos::where('session_id', $caixa)->with('session')->orderBy('id', 'ASC')->paginate(500);
    }

    public function getAllOrders($order = null, $colun = null)
    {
        if ($order != '') {
            return orderPos::where($colun, 'LIKE', '%' . $order . '%')
            ->where('company_id',Auth::user()->company_id)
            ->orderBy('id', 'DESC')->with('session')->paginate(100);
        } else {

            return orderPos::with('session')->orderBy('id', 'DESC')
            ->where('company_id',Auth::user()->company_id)
            ->paginate(100);
        }
    }
}
