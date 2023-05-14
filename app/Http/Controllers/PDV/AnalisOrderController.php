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
        ->whereYear('created_at', $year)->get();

        $operations = operation_caixa_type_session::where('operation_caixa_type_id',1)
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
        if (Auth::user()->nivel =='admin') {
            $dias = $day;
            $resul = [];
            $orders = [];
            for ($i=1; $i <= $dias; $i++) {
                $resul = $i;
                if ($i<=9) {
                    $resul = '0'.$i;
                }
            $order = orderPos::whereDay('created_at',$resul)
            ->whereYear('created_at',date('Y'))
            ->whereMonth('created_at',date('m'))->sum('total');
            $orders[] = $order;
            }
            return $orders;
        }
    }

    public function IntervalDateRelator($month=null,$year=null,$inicio,$fin)
    {
        $startDate = Carbon::parse("$year-$month-$inicio");
        $endDate = Carbon::parse("$year-$month-$fin");

        $endDate = date('Y-m-d H:i:s', strtotime($endDate.' +1 day -1 second'));

        $orders = DB::table('order_pos')->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $operations = DB::table('operation_caixa_type_sessions')->whereMonth('created_at',$month)
        ->whereYear('created_at',$year)
        ->whereBetween('created_at',[$startDate, $endDate])
        ->sum('amount');
        return $this->CalcularRelatorio($orders,$operations);
    }


    public function AgruparRelatorio(Request $request,PontoController $ponto)
    {
        if ($request->dados) {
            //A selectionar na tabela correspondente
            $select = DB::table($request->dados);
            //A verificar se tem resultado
            if ($select->count()>0) {
                //A trazer todos os dados correspondente
                $todos = $select->get();
                //A fazer foreach
                $resultado = [];
                $outro = [];
                $lucro = [];
                foreach ($todos as $dado) {
                    //a verificar o tipo da tabela
                    if ($request->dados == 'users') {
                        $outro[] = $dado->apelido;
                        //a selectionar todas a vendas corespondente
                        $vendas = DB::table('encomendas_pos')
                        ->where('estado','Pago')
                        ->where('id_responsavel',$dado->id);
                        //A verificar se tem algo nesta consuta
                        if ($vendas->count() > 0) {
                            $resultado[$dado->apelido] = $vendas->sum('total');
                        } else {
                            # code...
                        }

                    }else{
                        $outro[] = $dado->nome;
                        //a selectionar todas a vendas corespondente
                        $vendas = DB::table('encomendas_pos')
                        ->where('estado','Pago')
                        ->where('cliente',$dado->nome);
                        //A verificar se tem algo nesta consuta
                        if ($vendas->count() > 0) {
                            $resultado[$dado->nome] = $vendas->sum('total');
                        } else {
                            # code...
                        }
                    }
                }
                return json_encode(['result'=>$resultado,'outro'=>$outro]);
            }
        }elseif($request->colun0){
            if ($request->colun0 == 'data') {
                $data = new Data();
                $mes = date('m');      // Mês desejado, pode ser por ser obtido por POST, GET, etc.
                $ano = date("Y"); // Ano atual
                $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano)); // Mágica, plim!

                //A selectionar na tabela
                $resultado = [];
                $select = DB::table('encomendas_pos');
                if ($request->value == 'ano') {
                    $selects = $select
                    ->where('estado','Pago')
                    ->where($request->value,date('Y'));
                    $resultado[date('Y')] = $selects->sum('total');
                } elseif ($request->value == 'mes') {
                    $meses = $data->meses();
                    for ($i=0; $i <= 11; $i++) {
                        $selects = DB::table('encomendas_pos')
                        ->where('mes',$meses[$i])
                        ->where('estado','Pago')
                        ->sum('total');
                        $resultado[$meses[$i]] = $selects;
                    }
                }elseif ($request->value == 'semana'){
                    $segunda = date('d', strtotime('monday this week'));
                    //A pegar dias
                    $dia = $data->Data()['dia'];
                    //a verificar dias
                    if ($dia <= 7) {
                        $semana = 1;
                    }else{
                        $semana = $dia - 7;
                    }
                    //A fazer for
                    for ($i=$semana; $i <= date('d') ; $i++) {
                        $selects = DB::table('encomendas_pos')
                        ->where('dia',$i)
                        ->where('mes',$data->Data()['mes'])
                        ->where('ano',$data->Data()['ano'])
                        ->where('estado','Pago')
                        ->sum('total');
                        $resultado['dia '.$i] = $selects;
                    }
                }
                elseif ($request->value == 'hoje'){
                    $test = $ponto->BuscarRelatorio();
                    $resultado = $test;
                }elseif ($request->value == 'dia'){
                    $meses = $data->meses();
                    for ($i=01; $i <= $ultimo_dia; $i++) {
                        if ($i <=9) {
                            $i = '0'.$i;
                        }
                    $data = new Data();
                    $selects = DB::table('encomendas_pos')
                    ->where('dia',$i)
                    ->where('mes',$data->Data()['mes'])
                    ->where('ano',$data->Data()['ano'])
                    ->where('estado','Pago')
                    ->sum('total');
                    $resultado['dia '.$i] = $selects;
                    }
                }
                return ['result'=>$resultado];
            } else {
                if ($request->colun0 == 'cliente') {
                    $res = DB::table('cliente_pos')
                    ->where('nome',$request->value)
                    ->first();
                    $responsavel = $res->nome;
                } else {
                    $res = DB::table('users')
                    ->where('id',$request->value)
                    ->first();
                    $responsavel = $res->apelido;
                }

                $select = DB::table('encomendas_pos')
                ->where($request->colun0,$request->value)
                ->where('estado','Pago');
                //A verificar se dados mara trazer
                if ($select->count()>0) {
                    $resultado[$responsavel] = $select->sum('total');
                    return ['result'=>$resultado];
                }
            }
        }
    }
}
