<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
 
        $request->session()->regenerateToken();
        return Redirect::route('login');
    }
}
