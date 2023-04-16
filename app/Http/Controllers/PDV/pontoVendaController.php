<?php

namespace App\Http\Controllers\PDV;

use App\Http\Controllers\Controller;
use App\Models\caixa;
use App\Models\operation_caixa_type_session;
use App\Models\operationCaixaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use PhpParser\Node\Stmt\Return_;

class pontoVendaController extends Controller
{
    public function index()
    {
        return Inertia::render('pointSale', [
            'user' => Auth::user(),
            'data' => caixa::all()
        ]);
    }

    public function getOperation(operationCaixaType $operationCaixaType)
    {
        $operationCaixaTypes = $operationCaixaType::with(['operations' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(100);
        }])->get();

        $groupedOperations = [];

        foreach ($operationCaixaTypes as $operationCaixaType) {
            $type = $operationCaixaType->name;
            $operations = $operationCaixaType->operations->groupBy(function ($operation) {
                return $operation->created_at->format('Y-m-d');
            });
            foreach ($operations as $day => $ops) {
                $groupedOperations[$day][$type] = $ops;
            }
        }

        return $groupedOperations;
    }

    public function SaveOperation(Request $request, operationCaixaType $operationCaixaType)
    {
        if ($request->id) {
            operation_caixa_type_session::find($request->id)->update([
                'amount' => $request->amount,
                'subject' => $request->subject,
            ]);
            return $this->RespondSuccess('Atualizado com Sucesso', $this->getOperation($operationCaixaType));
        }
        operation_caixa_type_session::create([
            'amount' => $request->amount,
            'operation_caixa_type_id' => 1,
            'subject' => $request->subject,
            'user_id' => Auth()->user()->id,
        ]);

        return $this->RespondSuccess('Gasto Adicionado com Sucesso', $this->getOperation($operationCaixaType));
    }
}
