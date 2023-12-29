<?php

namespace App\Http\Controllers;

use App\classes\uploadImage;
use App\Models\app;
use App\Models\company;
use App\Models\Role;
use App\Models\type_license;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\typeLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

class StartController extends Controller
{
    public function index()
    {
        return Inertia::render('Start/Start');
    }

    public function saveCompany(Request $request,$locale)
    {
        app()->setLocale($locale);
        $request->validate([
            'company.name' => 'required|string',
            'company.nif' => 'required|unique:companies,nif',
            'company.phone' => 'required|numeric',
            'company.activity.name'=>'required',
            'user.name' => 'required|string',
            'user.email' =>  'required|unique:users,email',
            'user.phone' => 'required',
            'user.password' => 'required|min:6|max:50',
            'totals.month' => 'required||numeric',
            'license' => 'required',
            'company.currency_company.currency' => 'required',
            'company.currency_company.code' => 'required',
            'user.user_language.code'=>'required'
        ]);

        // $response = Http::withHeaders([
        //     'Authorization' => 'oEn34JE6gDfVuZlR6QRWX8Q2byn9repjspVFWoz2SZdncBYePGc7XoKZ8Noo',
        // ])->post('https://bosgc.sisgesc.net/api/SaveCompany',$request);

        // if ($response->successful()) {
            $company = $this->saveData($request,new uploadImage());
            return $this->ValidateLicense($request->license,$company,$request->user);
        // } else {
        //     $errorMessage = $response->body();
        //     return $this->RespondError($errorMessage);
        // }
    }

    public function saveData($request,$uploadImage)
    {
        $data = [];
        DB::transaction(function() use ($uploadImage, &$request ,&$data) {
            if ($request->company['imagem'] != null) {
                $company_img = $uploadImage->Upload('/company/image/',$request->company['imagem']);
            } else {
                $company_img = 'company.png';
            }
            if ($request->user['imagem'] != null) {
                $user_img = $uploadImage->Upload('/login/image/',$request->user['imagem']);
            } else {
                $user_img = 'user-286.png';
            }

            $company = company::create([
                'nif' => $request->company['nif'],
                'phone' => $request->company['phone'],
                'email'=>$request->user['email'],
                'name' => $request->company['name'],
                'activity_type_id' => $request->company['activity']['id'],
                'country' => $request->company['country']['name'],
                'image' => $company_img
            ]);
            $company->currencyCompany()->create($request->company['currency_company']);
            $armagem = $company->armagens()->create([
                'name' => $request->company['name']
            ]);

            $user = $company->users()->create([
                'role_id'=>1,
                'name' => $request->user['name'],
                'surname' => $request->user['name'],
                'email' => $request->user['email'],
                'phone' => $request->user['phone'],
                'password' => Hash::make($request->user['password']),
                'image' =>  $user_img,
                'armagen_id' => $armagem->id
            ]);

            $user->config()->create();
            $user->perfil()->create();
            $user->userLanguage()->create($request->user['user_language']);
            $data = $company;
        });
        return $data;
    }

    public function ValidateLicense($license,$company,$user)
    {
        $countApp = count(json_decode(json_encode($license), true));
        $licenseType = type_license::where('name',
        $countApp == 2 ? 'Basic' :
        ($countApp <=4 ? 'Premium' : 'Gold'))->first();
        $current_date = Carbon::now()->addDays(15);
        $crateLicense = $company->license()->create([
            'type_license_id' => $licenseType->id,
            'hash'=>Crypt::encrypt($current_date->format('Y-m-d')),
            'from' => now(),
            'to' => Carbon::now()->addDays(15),
            'state'=>'active'
       ]);
        foreach($license as $app) {
            $select = app::where('href',str_replace(' ','',$app['label']))->first();
            $crateLicense->app_license()->create([
                'company_id' => $company->id,
                'app_id' => $select->id
            ]);
        }

        $credencias = [
            'email' => $user['email'],
            "password" => $user['password'],
        ];

        Auth::attempt($credencias);

        $user = User::find(Auth::user()->id);

        $agent = new Agent();

        $browser = $agent->browser();

        $user->historic_login()->create([
            'company_id' => $user->company_id,
            'ip_address' => request()->ip(),
            'browser' => $browser,
        ]);

        return $this->RespondSuccess(__('Operation completed successfully'),$company);
    }

    public function ValidateLicenseFree($license,$company,$user)
    {
        $current_date = Carbon::now()->addDays(15);
        $license = $company->license()->create([
                'type_license_id' => 2,
                'from' => now(),
                'to' => Carbon::now()->addDays(15),
                'state'=>'active',
                'hash'=>Crypt::encrypt($current_date->format('Y-m-d')),
        ]);
        foreach (app::all() as $app) {
            $license->app_license()->create([
                'company_id' => $company->id,
                'app_id' => $app['id']
            ]);
        }

        $credencias = [
            'email' => $user['email'],
            "password" => $user['password'],
        ];

        Auth::attempt($credencias);

        $user = User::find(Auth::user()->id);

        $agent = new Agent();

        $browser = $agent->browser();

        $user->historic_login()->create([
            'company_id' => $user->company_id,
            'ip_address' => request()->ip(),
            'browser' => $browser,
        ]);
        return $this->RespondSuccess(__('Operation completed successfully'),$company);
    }

    public function welcome(company $company)
    {
        return Inertia::render('Start/Welcome',['company' => $company]);
    }

    function activeLicense(Request $request,company $companies)
    {

        $response = Http::withHeaders([
            'Authorization' => 'oEn34JE6gDfVuZlR6QRWX8Q2byn9repjspVFWoz2SZdncBYePGc7XoKZ8Noo',
        ])->post('https://bosgc.sisgesc.net/api/activeLicense',$request);

       $data = json_decode($response);

        if (!$data->data) {
            return $this->RespondError(__('Customer not found'));
        }

        $company = $companies->where('nif',$data->data->nif)->first();

        if (!$company) return $this->RespondError(__('A server error occurred'));
        if ($company->license->state == 'active') return $this->RespondError(__('This company has an active license'));

        $company->license->state = 'active';
        $company->license->to = $data->data->license->to;
        $company->license->from = $data->data->license->from;
        $company->license->hash = Crypt::encrypt($data->data->license->to);

        if (!$company->license->save()) return $this->RespondError(__('Error activating license, contact administrator'));
        return $this->RespondSuccess(__('Congratulations, your license has been successfully activated'));
    }
}
