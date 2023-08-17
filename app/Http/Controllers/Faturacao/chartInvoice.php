<?php

namespace App\Http\Controllers\Faturacao;

use App\classes\Data;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;

class chartInvoice extends Controller
{
    public function index()
    {
        $nowDate = Carbon::now();
        $month = $nowDate->daysInMonth;
        $date = new Data;
        $array = [];
        for ($i = 1; $i <= $month; $i++) {
            $totalInvoice = $this->companyUser()->invoice()->whereDay('DateOrder', $i <= 9 ? '0' . $i : $i)
                ->whereMonth('DateOrder', $nowDate->month)
                ->whereYear('DateOrder', $nowDate->year)
                ->sum('TotalInvoice');
            $array[] = $totalInvoice;
        }
        return $array;
    }

    public function users()
    {
        $array = [];
        $users = $this->companyUser()->users()->get();
        foreach ($users as $user) {
            array_push($array, [
                "total" => $this->companyUser()->invoice()->where('user_id', $user->id)->sum('TotalInvoice'),
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
            array_push($array, [
                "total" => $this->companyUser()->invoice()->where('state', $state[$i])->sum('TotalInvoice'),
                "name" => $state[$i]
            ]);
        }
        return $array;
    }

    public function data($type, $start = null, $end = null)
    {
        $now = Carbon::now();

        if ($type == 'now' || $type == 'subDay') {

            $total = $this->companyUser()->invoice()->where('DateOrder', $now->$type()->format('Y-m-d'))
                ->sum('TotalInvoice');
        } elseif ($type == 'month' || $type == 'subMonth') {

            $month = $type == 'month' ? $now->month : $now->month()->format('m');
            $total = $this->companyUser()->invoice()->whereMonth('DateOrder', $month)
                ->sum('TotalInvoice');
        } elseif ($type == 'week') {

            $week = $now->startOfWeek()->format('Y-m-d');
            $total = $this->companyUser()->invoice()->whereBetween('DateOrder', [$week, now()->format('Y-m-d')])
                ->sum('TotalInvoice');
        } else if ($type === 'firstOfQuarter' || $type === 'firstOfSemester') {

            $year = Carbon::now()->year;

            $startDate = Carbon::parse("$year-$start");

            $endDate = Carbon::parse("$year-$end");

            $total = $this->companyUser()->invoice()
                ->whereYear('DateOrder', $year)
                ->whereBetween('DateOrder', [$startDate, $endDate])
                ->sum('TotalInvoice');
        } else {
            $total = $total = $this->companyUser()->invoice()
                ->whereYear('DateOrder', $type)
                ->sum('TotalInvoice');
        }

        return [
            [
                'total' => $total,
                'name' => 'Total'
            ]
        ];
    }
}
