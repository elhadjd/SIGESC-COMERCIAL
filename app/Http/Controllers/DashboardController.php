<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function Dashboard(Request $request)
    {
        return Inertia::render('Dashboard',[
            'data'  => $request->user()->company()->first(),
            'user' => $request->user(),
        ]);
    }
}
