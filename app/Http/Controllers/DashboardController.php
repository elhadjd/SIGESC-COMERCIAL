<?php

namespace App\Http\Controllers;

use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function Dashboard(Request $request)
    {

        return Inertia::render('Dashboard',[
            'data'=> company::find($request->user()->id),
            'user' => $request->user(),
        ]);
    }
}
