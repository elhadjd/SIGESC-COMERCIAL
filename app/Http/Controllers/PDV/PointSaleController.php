<?php

namespace App\Http\Controllers\PDV;

use App\Http\Controllers\Controller;
use App\Models\caixa;
use App\Models\operation_caixa_type_session;
use App\Models\operationCaixaType;
use App\Models\orderPos;
use App\Models\Password_Invoice_Cancel;
use App\Models\paymentMethod;
use App\Models\produtos;
use App\Models\session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PointSaleController extends Controller
{
    public function index(caixa $caixa)
    {
        $data = $caixa->session()->where('user_id', Auth::user()->id)
            ->where('state', 'Aberto')
            ->orderBy('id', 'DESC')
            ->first();

        if (!$data) {
            return Redirect::route('pontodevenda');
        }

        return Inertia::render('PointSale/Pos', [
            'user' => Auth::user(),
            'data' => Auth::user()->company,
            'session' => $data
        ]);
    }

    public function getTypeOperation(operationCaixaType $operationCaixaType)
    {
        return $operationCaixaType::all();
    }

    public function addOperation(Request $request)
    {
        $data = $request->data;
        $data['user_id'] = Auth::user()->id;
        $data['company_id'] = Auth::user()->company_id;
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

    public function PasswordCash(Request $request, session $session)
    {
        $session->load('caixa');
        if (Hash::check($request->password['password'], $session->caixa->password)) {
            return $this->RespondSuccess('Logado');
        }
        return $this->RespondError('Dados encorretos');
    }

    public function getUsersAuthorized()
    {
        return Password_Invoice_Cancel::all();
    }

    public function CancelInvoice(Request $request, $user, orderPos $invoice, OrdersController $ordersController)
    {
        $request->validate([
            'password' => "required"
        ]);


        if ($user == null || !is_numeric($user))
            return $this->RespondError("Seleciona um usuario valido");

        $user = Password_Invoice_Cancel::where('user_id', $user)->first();

        if (!Hash::check($request->password, $user->password))
            return $this->RespondError('Senha incorreta');
        $order = $invoice->with(['items' => function ($query) {
            $query->with(['product' => function ($product) {
                $product->withSum('stock', 'quantity');
            }]);
        }])->whereId($invoice->id)->first();

        $ordersController->CancelInvoice($invoice);
        $invoice->number = 0;
        $invoice->save();

        return $this->RespondSuccess('Pedido de anulação enviado com sucesso', $order);
    }
}
