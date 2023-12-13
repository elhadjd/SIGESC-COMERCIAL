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

    public function getPaymentOrders(Request $request,paymentMethod $paymentMethod)
    {
        $locale = $request->user()->userLanguage->code ? $request->user()->userLanguage->code : 'en';
        return $paymentMethod->with(['payments'=>function($payments)
        {
            $payments->with(['invoice'=>function($invoice){
                $invoice->where('company_id',$this->companyUser()->id);
            }]);
        }])->with(['methodTranslate'=>function($translate) use ($locale){
            $translate->where('local',$locale);
        }])->get();
    }
}
