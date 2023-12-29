<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Config\configController;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Login');
    }
    public function login(Request $request,$locale = null)
    {
         return Crypt::encrypt('2025-01-01');

        $request->validate([
            'email' => ['required','email'],
            'password' => ['required']
        ]);
        $credentials = [
            'email' => $request->email,
            'password'=> $request->password,
        ];


        // return Hash::make($request->password);

        if (Auth::attempt($credentials)) {

            $user = User::find(Auth::user()->id);

            $agent = new Agent();


            if($request->session == 1){
                $request->session()->put('session_lifetime', 10080);
                $request->session()->regenerate();
            }

            $browser = $agent->browser();

            $user->historic_login()->create([
                'company_id' => $user->company_id,
                'ip_address' => $request->ip(),
                'browser' => $browser,
            ]);
            $request->session()->regenerate();
            return $this->UrlGuard($request,$locale);

        } else {
            return Inertia::render('Auth/Login', [
                'erro' => __('data incorrect.'),
            ]);
        }
    }

    public function UrlGuard(Request $request)
    {
        $rota = str_replace('/','',$request->path);
        $userLanguage = $request->user()->userLanguage;
        if ($request->locale && $userLanguage == null) {
            $insertLang = new configController;
            $locale = [
                'code'=>$request->locale['local'],
                'language'=>$request->locale['name']
            ];
            $insertLang->insertLangUser(Auth::user(),$locale);
        }

        if ($rota != "" && $rota != "authlogin") {
            return Redirect::route($rota);
        } else {
            return redirect()->intended('/');
        }
    }

    public function saveCompany(Request $request)
    {
        return $request;
    }
}
