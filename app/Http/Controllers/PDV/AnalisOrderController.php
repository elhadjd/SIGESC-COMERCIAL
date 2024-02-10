<?php

namespace App\Http\Controllers\PDV;

use App\classes\Data;
use App\Http\Controllers\Controller;
use App\Models\operation_caixa_type_session;
use App\Models\operationCaixaType;
use App\Models\orderPos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalisOrderController extends Controller
{
    public function getRelatorByMonth($mouth = null, $year = null)
    {
        $orders = orderPos::whereMonth('created_at', $mouth)
        ->where('company_id',Auth::user()->company_id)
        ->whereYear('created_at', $year)->get();

        $operations = operation_caixa_type_session::where('operation_caixa_type_id',1)
        ->where('company_id',Auth::user()->company_id)
        ->whereMonth('created_at', $mouth)
        ->whereYear('created_at', $year)->sum('amount');


        return $this->CalcularRelatorio($orders,$operations);
    }

    public function CalcularRelatorio($orders,$operations)
    {
        $response = [
            'TotalVenda'=>0,
            'CustoProd'=>0,
            'TotalGasto'=>0,
            'TotalLucro'=>0,
        ];
        $response['TotalVenda'] = $orders->sum('total');
        $response['CustoProd'] = $orders->sum('total_costs');
        $response['TotalGasto'] = $operations;
        $response['TotalLucro'] = $response['TotalVenda'] - $response['CustoProd'] - $operations;
        return response()->json(['relat'=>$response,'encomendas'=>$orders]);
    }

    public function analisDay($day)
    {
        if (request()->user()->hasRole('User')) return $this->RespondError(__('User without access'));
        $dias = $day;
        $resul = [];
        $orders = [];
        for ($i=1; $i <= $dias; $i++) {
            $resul = $i;
            if ($i<=9) {
                $resul = '0'.$i;
            }
        $order = orderPos::whereDay('created_at',$resul)
        ->where('company_id',Auth::user()->company_id)
        ->whereYear('created_at',date('Y'))
        ->whereMonth('created_at',date('m'))->sum('total');
        $orders[] = $order;
        }
        return $orders;
    }

    public function IntervalDateRelator($month=null,$year=null,$inicio,$fin)
    {
        $startDate = Carbon::parse("$year-$month-$inicio");
        $endDate = Carbon::parse("$year-$month-$fin");

        $endDate = date('Y-m-d H:i:s', strtotime($endDate.' +1 day -1 second'));

        $orders = $this->companyUser()->OrderPdv()->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $operations = DB::table('operation_caixa_type_sessions')->whereMonth('created_at',$month)
        ->where('company_id',Auth::user()->company_id)
        ->whereYear('created_at',$year)
        ->whereBetween('created_at',[$startDate, $endDate])
        ->where('id',1)
        ->sum('amount');
        return $this->CalcularRelatorio($orders,$operations);
    }
}
