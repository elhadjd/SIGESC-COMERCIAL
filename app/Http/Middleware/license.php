<?php

namespace App\Http\Middleware;

use App\Models\company;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class License
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $company = company::find(Auth::user()->company_id);
        $user = User::find(Auth::user()->id);
        $license = $company->license()->first();

        $hash = Crypt::decrypt($license->hash);
        if ($license->state === 'blocked' || $license->state === 'inactive' || $license->state === 'expired') {
            return Redirect::route('LicenseBlocked');
        } else if ($hash != $license->to) {

            $license->state = 'blocked';
            $license->save();
            return Redirect::route('LicenseBlocked');

        } else {
            $finally_login = $user->historic_login()->orderBy('id', 'DESC')->first();
            $data_format = Carbon::parse($finally_login->created_at)->format('d-m-Y');
            $current_date = Carbon::now();
            if ($license->to < $current_date->format('Y-m-d')) {
                $license->state = 'expired';
                $license->save();
                return Redirect::route('LicenseBlocked');
            }
            if ($data_format > $current_date->format('d-m-Y')) {
                return Redirect::route('LicenseBlocked');
            }

        }

        return $next($request);
    }
}
