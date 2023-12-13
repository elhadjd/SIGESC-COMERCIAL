<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class localMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()){
            if ($request->user()->userLanguage != null) {
                $locale = $request->user()->userLanguage->code;
            }else{
                $locale = 'en';
            }
        }else{
            $locale = $request->route('local') ?$request->route('local'):'en';
        }
        App::setLocale($locale);
        return $next($request);
    }
}
