<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentInvoice;
use App\Models\paymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MethodsPaymentController extends Controller
{
    public function getMethods()
    {
        return paymentMethod::all();
    }

    public function getPayments(Invoice $invoice)
    {
        return $invoice->where('company_id',Auth::user()->company_id)->payments()->with('method')->get();
    }

    public function getPaymentOrders(paymentMethod $paymentMethod)
    {
        return $paymentMethod->with(['payments'=>function($payments)
        {
            $payments->with('invoice');
        }])->get();
    }
}
