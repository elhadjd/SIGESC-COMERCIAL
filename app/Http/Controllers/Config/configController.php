<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class configController extends Controller
{
    public function index()
    {
        return Inertia::render('Config');
    }
    public function users(Request $request)
    {
        return company::find($request->user()->id)->load('users');
    }
    public function getConfig(Request $request)
    {
        return company::find($request->user()->id)
        ->load('license')->load('users');
    }
}
