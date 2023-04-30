<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function index()
    {
        return Inertia::render('Login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $credencias = [
            'email' => $request->email,
            "password" => $request->password,
        ];

        if (Auth::attempt($credencias)) {
            return $this->UrlGuard($request);
        } else {
            return Inertia::render('Login', [
                'erro' => "dados do usuario incorrecto"
            ]);
        }
    }

    public function UrlGuard(Request $request)
    {
        $rota = str_replace('/','',$request->path);

        if ($rota != "" && $rota != "authlogin") {
            return Redirect::route($rota);
        } else {
          return Redirect::route('dashboard');
        }
    }


    public function saveCompany(Request $request)
    {
        return $request;
    }
}
