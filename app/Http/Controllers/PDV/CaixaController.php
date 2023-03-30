<?php

namespace App\Http\Controllers\PDV;

use App\Http\Controllers\Controller;
use App\Models\caixa;
use App\Models\operation_caixa_type_session;
use App\Models\operationCaixaType;
use App\Models\orderPos;
use App\Models\paymentMethod;
use App\Models\session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CaixaController extends Controller
{
  public function get(caixa $caixa)
  {
    return $caixa->with(['session' => function($query){
        $query->where('state','Aberto')
        ->orderBy('id','DESC');
    }])->get();

  }
  public function show(session $session)
  {
    $session->load('orders');
    return $this->sumData($session);
  }

  public function sessions(caixa $caixa)
  {
    return $caixa->with(['session'=>function($session){
        $session->orderBy('id','DESC');
    }])->first();
  }

  public function sumData($session)
  {
    $methods = paymentMethod::withSum(['paymentsPdv'=>function($payments) use ($session) {
        $payments->where('session_id', $session->id);
    }], 'amountPaid')->get();

    $orders = session::withSum('orders','total')->where('id',$session->id)->first();

    $operations = operationCaixaType::withSum(['operations'=>function($operations)use ($session){
        $operations->where('session_id',$session->id);
    }],'amount')->get();


    return response()->json([
        'operations'=> $operations,
        'orders' => $orders,
        'methods' => $methods,
    ]);
  }

  public function opiningControl(Request $request, $session = null)
  {
    if ($session) {
        $session = session::find($session);
        $session->opening = $request->amount;
        $session->observation_opining = $request->observation;
        $session->state = "Aberto";
        $session->save();
        $session->caixa()->update([
            'state'=>'Aberto'
        ]);
        $session->load('caixa');
        return $session;
    }

    $caixa = caixa::find($request->id);

    $data = $caixa->session()->where('user_id',Auth::user()->id)
       ->where('state','Aberto')
        ->orderBy('id','DESC')
        ->first();

    if ($data) return Redirect::route('pos',$request->id);
    $caixa->update([
        'state' => 'A abrir'
    ]);
    $sessions = session::create([
        'caixa_id' => $request->id,
        'user_id' => $request->user()->id,
        'state' => 'Aberto',
    ]);
    $session = session::find($sessions->id);
    return Redirect::route('pos',$request->id);
  }

  public function clossSession(Request $request , session $session)
  {
    $session->cash = $request->informado;
    $session->orders_values = $request->total;
    $session->state = 'Fechado';
    $session->save();
    $session->caixa()->update([
        'state'=>'Fechado'
    ]);

    $session->load('orders');
    return $this->sumData($session);
  }

  public function updateSession(session $session)
  {
    $session->state = "Aberto";
    $session->cash = 0;
    $session->save();
    $session->caixa()->update([
        'state'=>"Aberto"
    ]);
    $session->load('orders');
    return $this->sumData($session);
  }

}