<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\produtos;
use Illuminate\Support\Facades\DB;

class searchController extends Controller
{
    public function filter($table,$column,$tex)
    {
        if ($table == 'produtos') {
           return produtos::where($column,'LIKE','%'.$tex.'%')->withSum('stock','quantity')->get();
        }
        if ($table == 'invoices') {
            return Invoice::where($column,'LIKE','%'.$tex.'%')->get();
        }
        return DB::table($table)->where($column,'LIKE','%'.$tex.'%')->get();
    }
}
