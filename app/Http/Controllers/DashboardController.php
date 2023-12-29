<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function Dashboard(Request $request)
    {
        $locale = app()->getLocale();
        return Inertia::render('Dashboard/index',[
            'data' => $request->user()->company()
            ->with(['license'=>function($license) use ($locale){
                $license->with(['app_license'=>function($app_license) use ($locale){
                    $app_license->with(['apps'=>function($apps) use ($locale){
                        $apps->with(['appTranslate'=>function($translate) use ($locale){
                            $translate->where('local',$locale);
                        }]);
                    }]);
                }]);
            }])->first(),
            'user' => $request->user(),
        ]);
    }
}
