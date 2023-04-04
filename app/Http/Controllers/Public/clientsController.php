<?php

namespace App\Http\Controllers\public;

use App\Http\Controllers\Controller;
use App\Models\cliente;
use Illuminate\Http\Request;

class clientsController extends Controller
{
    public function get()
    {
        return cliente::all();
    }

    public function getClientsCredit(cliente $clients)
    {
        return $clients->with('invoices')->withSum('invoices','TotalInvoice')->withSum('invoices','RestPayable')->get();
    }
}