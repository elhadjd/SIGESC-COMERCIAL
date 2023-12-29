<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\armagen;
use App\Models\movement_type;
use App\Models\movement_type_produtos;
use App\Models\produtos;
use App\Models\stock;
use App\Models\transfer;
use App\Models\transferItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function get()
    {
        return $this->companyUser()->transfer()->orderBy('id','DESC')->paginate(100);
    }

    public function show(transfer $order)
    {
        return $order->with(['items'=>function($items){
            $items->orderBy('id','desc')->get();
        }])->whereId($order->id)->first();
    }

    public function create(Request $request,transfer $transfer)
    {
        $number = 'TF'.date('d').'-'.date('m').'-'.date('Y').'/';
        $create = $this->companyUser()->transfer()->create([
            'orderNumber'=> $number,
            'user_id' => $request->user()->id,
            'store_principal_id' => $request->user()->armagen_id
        ]);

        $model = transfer::find($create->id);
        $model->orderNumber = $model->orderNumber.$model->id;
        $model->save();

        return $this->show($model);
    }

    public function addItem(Request $request,produtos $product, transfer $order)
    {
        $order->items()->create([
            'produtos_id' => $product->id,
            'priceSold' => $product->preçocust,
            'final_price' => $product->preçocust,
            'armagen_id'=>$request->user()->armagen_id,
            'total' => $product->preçocust,
            'quantity'=> 1
        ]);

        $order->total += $product->preçocust;
        $order->totalMerchandise += $product->preçocust;
        $order->save();
        return $this->show($order);

    }

    public function checkQuantity($item)
    {
        $message = [
            'state'=> false,
            'message'=>null
        ];
        $transfer = transfer::find($item['transfer_id']);
        $store = stock::where('armagen_id',$item['armagen_id'])->where('produtos_id',$item['produtos_id']);
        if ($transfer->store_destination_id == null) {
            $message['state'] = true;
            $message['message'] = __('Please select a transfer destination warehouse');
        }else
        if ($transfer->store_destination_id == $item['armagen_id']){
            $message['state'] = true;
            $message['message'] = __('The selected warehouse cannot be the same as the transfer destination warehouse');
        }else
        if ($store->count()<=0) {
            $message['state'] = true;
            $message['message'] = __('This product is not in stock in this selected warehouse');

        }else if ($store->first()->quantity < $item['quantity']) {
            $message['state'] = true;
            $message['message'] = __('This product is not in stock in this selected warehouse');
        };
        return $message;
    }

    public function update(Request $request, transferItem $item)
    {
        $request->validate([
            'store'=> 'required',
        ]);
        if ($this->checkQuantity($request->all())['state']) return $this->RespondWarn($this->checkQuantity($request->all())['message'],$this->sumItems($request->transfer_id));
        $data = $request->all();
        unset($data['product'], $data['created_at'] , $data['updated_at']);
        $spend = $data['spent'] / $data['quantity'];
        $data['final_price'] = $data['priceSold'] + $spend;
        $data['total'] = $data['priceSold'] * $data['quantity'] + $data['spent'];

        $item->update($data);

        return $this->sumItems($request->transfer_id);
    }

    public function DeleteItem($order,transferItem $item)
    {
       $item->delete();
       return $this->sumItems($order);
    }

    public function addLocalDestine(transfer $order,$store)
    {
        $order->store_destination_id = $store;
        $order->save();
        return $order->fresh()->store_to;
    }

    public function addDateOrder(Request $request,$type,transfer $order)
    {
        $order[$type] = $request[$type];
        $order->save();
    }


    public function sumItems($id)
    {
        $transfer = transfer::find($id)->withSum('items','total')
        ->withSum('items','spent')->whereId($id)->first();

        $transfer->total = $transfer->items_sum_total;
        $transfer->total_spent = $transfer->items_sum_spent;
        $transfer->totalMerchandise = $transfer->items_sum_total - $transfer->items_sum_spent;
        $transfer->save();

        return $this->show($transfer);
    }

    public function saveTransfer(Request $request,transfer $order,$type,stock $stock)
    {
        $order->load('items');
        if ($type == 'cancel' && $order->state == 'Anulado') return $this->RespondError(__('This purchase has already been canceled.'),$order);
        if ($type == 'cancel' && $order->state == 'Cotação') return $this->RespondInfo(__('It is not possible to cancel an unconfirmed order'),$order);
        if ($order->items->count() <= 0) return $this->RespondInfo(__('You must add at least one product to the order'),$order);
        $message = [
            'state'=> 0,
            'message'=> null,
            'data'=> []
        ];
        DB::transaction(function() use ($order,&$message,&$stock,&$request,$type){
            foreach ($order->items as $item) {
                if ($type == 'cancel') $item['armagen_id'] = $order->store_destination_id;
                if ($type == 'cancel' ? $this->checkQuantityCancel($item)['state'] : $this->checkQuantity($item)['state']) {
                    $message['state'] = 1;
                    $message['message'] = $this->checkQuantityCancel($item)['message'];
                    $message['data'] = $item;
                    break;
                }
            }
            if (!$message['state']) {
                foreach ($order->fresh()->items as $item) {
                    $stock_final =  $stock->where('produtos_id',$item['produtos_id'])
                    ->where('armagen_id',$item['armagen_id'])->first();
                    $stock_final->update([
                        'quantity' => $type == 'save' ? $stock_final->quantity - $item['quantity'] : $stock_final->quantity + $item['quantity']
                    ]);

                    $stockDestination = $stock->where('produtos_id',$item['produtos_id'])
                    ->where('armagen_id',$order['store_destination_id']);

                    if ($stockDestination->count() > 0) {
                        $quantityAfters = $stockDestination->first()->quantity;
                        $stockDestination->update([
                            'quantity' => $type == 'cancel' ? $quantityAfters - $item['quantity'] : $quantityAfters + $item['quantity']
                        ]);
                    } else {
                        $stockDestination->create([
                            'produtos_id' => $item['produtos_id'],
                            'armagen_id' => $order['store_destination_id'],
                            'quantity' => $item['quantity']
                        ]);
                    }


                    $quantityAfter = $stock_final->quantity;

                    $movementTypes = movement_type::where('name', 'Transferencia')->first();

                    movement_type_produtos::create([
                        'company_id' => Auth::user()->company_id,
                        'user_id' => $request->user()->id,
                        'produtos_id' => $item->product['id'],
                        'movement_type_id' => $movementTypes->id,
                        'armagen_id' => $stock_final->armagen_id,
                        'quantity' => $item['quantity'],
                        'price_cost' => $item->product['preçocust'],
                        'price_sold' => $item['priceSold'],
                        'motive' => $type == 'save' ? "Transferencia":'Transferencia cancelada',
                        'quantityAfter' => $quantityAfter,
                    ]);
                }
                $order->state = $type == 'save' ? "Publicado":'Anulado';
                $order->save();
                $message['state'] = 2;
                $message['message'] = $type == 'save' ? 'Transferencia Confirmada ' : 'Transferencia Anulada Com sucesso';
            }
        });
        if($message['state'] == 2) return $this->RespondWarn($message['message'],$order);
        if ($message['state'] == 1) {
            return $this->RespondWarn($message['message'].$message['data']['product']['nome'],$order);
        }
        return $this->show($order);
    }

    public function checkQuantityCancel($item)
    {
        $message = [
            'state'=> false,
            'message'=>null
        ];
        $store = stock::where('armagen_id',$item['armagen_id'])->where('produtos_id',$item['produtos_id']);
        if ($store->count()<=0) {
            $message['state'] = true;
            $message['message'] = 'Este produto não tem quantidade neste armagen selecionado ';
        }else if ($store->first()->quantity < $item['quantity']) {
            $message['state'] = true;
            $message['message'] = 'Este produto não tem quantidade suficiente neste armagen selecionado';
        };
        return $message;
    }
}
