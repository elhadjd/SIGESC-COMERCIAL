<?php

namespace App\Http\Controllers\PDV;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class chartPdvController extends Controller
{
    public function index()
    {
        $nowDate = Carbon::now();
        $month = $nowDate->daysInMonth;
        $array = [];
        for ($i = 1; $i <= $month; $i++) {
            $totalInvoice = $this->companyUser()->OrderPdv()->whereDay('created_at', $i <= 9 ? '0' . $i : $i)
                ->whereMonth('created_at', $nowDate->month)
                ->whereYear('created_at', $nowDate->year)
                ->sum('total');
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
                "total" => $this->companyUser()->OrderPdv()->where('user_id', $user->id)->sum('total'),
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
            "Anulado",
        ];

        for ($i = 0; $i < count($state); $i++) {
            array_push($array, [
                "total" => $this->companyUser()->OrderPdv()->where('state', $state[$i])->sum('total'),
                "name" => $state[$i]
            ]);
        }
        return $array;
    }

    public function data($type, $start = null, $end = null)
    {
        $now = Carbon::now();

        if ($type == 'now' || $type == 'subDay') {

            $total = $this->companyUser()->OrderPdv()->where('created_at', $now->$type()->format('Y-m-d'))
                ->sum('total');
        } elseif ($type == 'month' || $type == 'subMonth') {

            $month = $type == 'month' ? $now->month : $now->month()->format('m');
            $total = $this->companyUser()->OrderPdv()->whereMonth('created_at', $month)
                ->sum('total');
        } elseif ($type == 'week') {
            $week = $now->startOfWeek()->format('Y-m-d');
            $endDate = date('Y-m-d H:i:s', strtotime(now()->format('Y-m-d').' +1 day -1 second'));
            $total = $this->companyUser()->OrderPdv()->whereBetween('created_at', [$week, $endDate])
                ->sum('total');
        } else if ($type === 'firstOfQuarter' || $type === 'firstOfSemester') {

            $year = Carbon::now()->year;

            $startDate = Carbon::parse("$year-$start");

            $endDate = Carbon::parse("$year-$end");
            $endDate = date('Y-m-d H:i:s', strtotime($endDate.' +1 day -1 second'));

            $total = $this->companyUser()->OrderPdv()
                ->whereYear('created_at', $year)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total');
        } else {
            $total = $total = $this->companyUser()->OrderPdv()
                ->whereYear('created_at', $type)
                ->sum('total');
        }

        return [
            [
                'total' => $total,
                'name' => 'Total'
            ]
        ];
    }
}
