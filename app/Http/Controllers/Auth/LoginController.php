<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $credential = [
            'email' => $request->email,
            "password" => $request->password,
        ];

        if (Auth::attempt($credential)) {

            $user = User::find(Auth::user()->id);

            $agent = new Agent();

            $browser = $agent->browser();

            $user->historic_login()->create([
                'company_id' => $user->company_id,
                'ip_address' => $request->ip(),
                'browser' => $browser,
            ]);

            return $this->UrlGuard($request);

        } else {
            return Inertia::render('Auth/Login', [
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