<?php

namespace App\Http\Controllers\Compra;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class chartPurchase extends Controller
{
    public function index()
    {
        $nowDate = Carbon::now();
        $month = $nowDate->daysInMonth;
        $array = [];
        for ($i = 1; $i <= $month; $i++) {
            $totalInvoice = $this->companyUser()->purchase()->whereDay('DateOrder', $i <= 9 ? '0' . $i : $i)
                ->whereMonth('DateOrder', $nowDate->month)
                ->whereYear('DateOrder', $nowDate->year)
                ->sum('total');
            $array[] = $totalInvoice;
        }
        return $array;
    }

    public function users()
    {
        $array = [];
        $users = User::where('company_id', Auth::user()->company_id)->get();
        foreach ($users as $key => $user) {
            array_push($array, [
                "total" => $this->companyUser()->purchase()->where('user_id', $user->id)->sum('total'),
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
                "total" => $this->companyUser()->purchase()->where('state', $state[$i])->sum('total'),
                "name" => $state[$i]
            ]);
        }
        return $array;
    }

    public function data($type, $start = null, $end = null)
    {
        $now = Carbon::now();

        if ($type == 'now' || $type == 'subDay') {

            $total = $this->companyUser()->purchase()
            ->where('DateOrder', $now->$type()->format('Y-m-d'))
            ->sum('total');
        } elseif ($type == 'month' || $type == 'subMonth') {
            $month = $type == 'month' ? $now->month : $now->month()->format('m');
            $total = $this->companyUser()->purchase()->whereMonth('DateOrder', $month)
            ->sum('total');

        } elseif ($type == 'week') {
            $week = $now->startOfWeek()->format('Y-m-d');
            $total = $this->companyUser()->purchase()->whereBetween('DateOrder', [$week, now()->format('Y-m-d')])
            ->sum('total');
        }else if ($type === 'firstOfQuarter' || $type === 'firstOfSemester') {

            $year = Carbon::now()->year;

            $startDate = Carbon::parse("$year-$start");

            $endDate = Carbon::parse("$year-$end");

            $total = $this->companyUser()->purchase()
                ->whereYear('DateOrder', $year)
                ->whereBetween('DateOrder', [$startDate, $endDate])
                ->sum('total');

        } else{
            $total = $total = $this->companyUser()->purchase()
            ->whereYear('DateOrder',$type)
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
