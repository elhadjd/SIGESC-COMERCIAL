<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function Dashboard(Request $request,$local)
    {
        return Inertia::render('Dashboard/index',[
            'data' => $request->user()->company()
            ->with(['license'=>function($license) use ($local){
                $license->with(['app_license'=>function($app_license) use ($local){
                    $app_license->with(['apps'=>function($apps) use ($local){
                        $apps->with(['appTranslate'=>function($translate) use ($local){
                            $translate->where('local',$local);
                        }]);
                    }]);
                }]);
            }])->first(),
            'user' => $request->user(),
        ]);
    }
}
