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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CaixaController extends Controller
{
    public function get(caixa $caixa)
    {
        return $caixa->with(['session' => function($query){
            $query->where('state','Aberto')
            ->orderBy('id','DESC');
        }])->where('company_id',Auth::user()->company_id)->get();

    }
    public function show($local,session $session)
    {
        $session->load('orders');
        return $this->sumData($session,$local);
    }


    public function sessions(caixa $caixa)
    {
    return $caixa->session()->orderBy('id','desc')->limit(100)->get();
    }

    public function sumData($session,$local)
    {
        $methods = PaymentMethod::withSum(['paymentsPdv' => function($payments) use ($session) {
            $payments->where('session_id', $session->id);
        }], 'amountPaid')
        ->withSum(['paymentsPdv' => function($payments) use ($session) {
            $payments->where('session_id', $session->id);
        }], 'change')->with(['methodTranslate'=>function($translate) use ($local){
            $translate->where('local',$local);
        }])
        ->get();

        $orders = session::withSum(['orders' => function($session){
            $session->where('state','Pago');
        }], 'total')->whereId($session->id)->first();

        $operations = operationCaixaType::withSum(['operations'=>function($operations)use ($session){
            $operations->where('session_id',$session->id);
        }],'amount')
        ->with(['operationTranslate'=>function($translate) use ($local){
            $translate->where('local',$local);
        }])
        ->get();

        return response()->json([
            'operations'=> $operations,
            'orders' => $orders,
            'methods' => $methods,
            'length' => $session->orders()->count()
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

    public function clossSession(Request $request ,$local,session $session)
    {
        $session->cash = $request->informado;
        $session->orders_values = $request->total;
        $session->state = 'Fechado';
        $session->save();
        $session->caixa()->update([
            'state'=>'Fechado'
        ]);

        $session->load('orders');
        return $this->sumData($session,$local);
    }

    public function updateSession($local,session $session)
    {
        $session->state = "Aberto";
        $session->cash = 0;
        $session->save();
        $session->caixa()->update([
            'state'=>"Aberto"
        ]);
        $session->load('orders');
        return $this->sumData($session,$local);
    }

    public function savePoint(Request $request, $caixa = null)
    {
        $request->validate([
            'name'=>'required',
            'user'=>'required',
        ]);
        if ($caixa!=null) {
        $caixa = caixa::find($caixa);
        $caixa->name = $request->name;
        $caixa->user_id = $request->user['id'];
        if ($request->password) $caixa->password = Hash::make($request->password);

        if ($caixa->save()) return $this->RespondSuccess('Dados atualizado com sucesso');

        return $this->RespondError('Erro ao atualizar os dados');
        }

        caixa::create([
            'name'=>$request->name,
            'password'=>Hash::make($request->password),
            'user_id'=>$request->user['id'],
            'company_id'=>$request->user['company_id'],
        ]);
        return $this->RespondSuccess('Caixa adicionada com sucesso');
    }

    public function deleteCash(Request $request,caixa $caixa){
        $session = $caixa->session->count();
        if ($session > 0) return $this->RespondError('Não é posivel eliminar esta caixa, pois ele posui um ou mais sesões');

        $caixa->delete();

    }
}
