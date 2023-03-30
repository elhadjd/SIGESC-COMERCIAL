<?php

namespace App\Http\Controllers\PDV;

use App\Http\Controllers\Controller;
use App\Models\caixa;
use App\Models\operation_caixa_type_session;
use App\Models\operationCaixaType;
use App\Models\paymentMethod;
use App\Models\session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PointSaleController extends Controller
{
    public function index(caixa $caixa)
    {
       $data = $caixa->session()->where('user_id',Auth::user()->id)
       ->where('state','Aberto')
        ->orderBy('id','DESC')
        ->first();

        if (!$data) {
            return Redirect::route('pontodevenda');
        }

        return Inertia::render('Pos',[
            'session'=>$data
        ]);
    }

    public function getTypeOperation(operationCaixaType $operationCaixaType)
    {
        return $operationCaixaType::all();
    }

    public function addOperation(Request $request)
    {
        $data = $request->data;
        $data['user_id'] = $request->user()->id;
        if (operation_caixa_type_session::create($data)) {
            return $this->RespondSuccess('Success');
        }
    }

    public function menuPos()
    {
        return response()->json([
            'User' => Auth::user(),
            'methods' => paymentMethod::all()
        ]);
    }

    public function PasswordCash(Request $request,session $session)
    {
        $session->load('caixa');
        if (Hash::check($request->password['password'],$session->caixa->password)) {
            return $this->RespondSuccess('Logado');
        }
        return $this->RespondError('Dados encorretos');
    }
}
