<?php

namespace App\Http\Controllers\Faturacao;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class chartInvoice extends Controller
{
    public function index()
    {
        $nowDate = Carbon::now();
        $month = $nowDate->daysInMonth;
        $array = [];
        for ($i = 1; $i <= $month; $i++) {
            $totalInvoice = $this->companyUser()->invoice()->whereDay('DateOrder', $i <= 9 ? '0' . $i : $i)
                ->whereMonth('DateOrder', $nowDate->month)
                ->whereYear('DateOrder', $nowDate->year)
                ->sum('TotalInvoice');

            $totalCusto = $this->companyUser()->invoice()
            ->whereDay('DateOrder', $i <= 9 ? '0' . $i : $i)
            ->whereMonth('DateOrder', $nowDate->month)
            ->whereYear('DateOrder', $nowDate->year)
            ->sum('TotalCusto');

            $totalLucro = $totalInvoice - $totalCusto;
                
            // $array = [
            //     'total' => $totalInvoice,
            //     'lucro' => $totalLucro
            // ];

            $array[] = $totalInvoice;
        }

        return $array;
    }

    public function users()
    {
        $array = [];
        $users = $this->companyUser()->users()->get();
        foreach ($users as $user) {
            $totalInvoice = $this->companyUser()->invoice()->where('user_id', $user->id)->sum('TotalInvoice');
            $totalCusto = $this->companyUser()->invoice()->where('user_id', $user->id)->sum('TotalCusto');

            array_push($array, [
                "total" => $totalInvoice,
                "lucro" => $totalInvoice - $totalCusto,
                "name" => $user->surname
            ]);
        }
        return $array;
    }

    public function state()
    {
        $array = [];

        $state =  [
            "Pago",
            'Publicado',
            "Anulado",
            "Cotação"
        ];

        for ($i = 0; $i < count($state); $i++) {

           $totalInvoice = $this->companyUser()->invoice()->where('state', $state[$i])->sum('TotalInvoice');
           $totalCusto  = $this->companyUser()->invoice()->where('state', $state[$i])->sum('TotalCusto');

            array_push($array, [
                "total" => $totalInvoice,
                "lucro" => $totalInvoice - $totalCusto,
                "name" => $state[$i]
            ]);
        }
        return $array;
    }

    public function data($type, $start = null, $end = null)
    {
        $now = Carbon::now();

        if ($type == 'now' || $type == 'subDay') {

            $total = $this->companyUser()->invoice()->where('DateOrder', $now->$type()->format('Y-m-d'))->sum('TotalInvoice');

        } elseif ($type == 'month' || $type == 'subMonth') {

            $month = $type == 'month' ? $now->month : $now->month()->format('m');
            $total = $this->companyUser()->invoice()->whereMonth('DateOrder', $month)->sum('TotalInvoice');

        } elseif ($type == 'week') {

            $week = $now->startOfWeek()->format('Y-m-d');
            $total = $this->companyUser()->invoice()->whereBetween('DateOrder', [$week, now()->format('Y-m-d')])->sum('TotalInvoice');

        } else if ($type === 'firstOfQuarter' || $type === 'firstOfSemester') {

            $year = Carbon::now()->year;

            $startDate = Carbon::parse("$year-$start");

            $endDate = Carbon::parse("$year-$end");

            $total = $this->companyUser()->invoice()
                ->whereYear('DateOrder', $year)
                ->whereBetween('DateOrder', [$startDate, $endDate])
                ->sum('TotalInvoice');
        } else {
            $total = $total = $this->companyUser()->invoice()->whereYear('DateOrder', $type)->sum('TotalInvoice');
        }

        return [
            [
                'total' => $total,
                'name' => 'Total'
            ]
        ];
    }
}
