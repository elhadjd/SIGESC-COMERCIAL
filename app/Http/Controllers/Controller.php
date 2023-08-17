<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Public\ResponseMessage;
use App\Http\Controllers\Public\CompanyUser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests,ResponseMessage,CompanyUser;
}
