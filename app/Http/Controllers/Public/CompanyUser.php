<?php

namespace App\Http\Controllers\Public;

use App\Models\company;
use Illuminate\Support\Facades\Auth;

trait CompanyUser

{
    function companyUser() {
        return company::find(Auth::user()->company_id);
    }
}
