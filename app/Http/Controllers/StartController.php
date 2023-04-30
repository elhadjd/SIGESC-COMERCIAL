<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\public\imagensController;
use App\Models\app;
use App\Models\armagen;
use App\Models\company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class StartController extends Controller
{
    public function index()
    {
        return Inertia::render('Start');
    }

    public function saveCompany(Request $request,imagensController $uploadImage)
    {
        $request->validate([
            'company.name' => 'required|string',
            'company.nif' => 'required|unique:companies,nif',
            'company.phone' => 'required|numeric',
            'user.name' => 'required|string',
            'user.email' =>  'required|email|unique:users,email',
            'user.phone' => 'required',
            'user.password' => 'required|min:6|max:50',
            'license' => 'required'
        ]);

        if ($request->license === 'free') {
           $company = $this->saveData($request,$uploadImage);
           return $this->ValidateLicenseFree($request->license,$company,$request->user);
        } elseif ($request->license === 'Premium') {
            $this->saveData($request,$uploadImage);
            $this->ValidateLicense($request->license);
        }
    }

    public function saveData($request,$uploadImage)
    {
        $data = [];
        DB::transaction(function() use ($uploadImage, &$request ,&$data) {
            if ($request->company['imagem'] != null) {
                $company_img = $uploadImage->UploadImage('/company/image/',$request->company['imagem']);
            } else {
                $company_img = 'produto-sem-imagem.png';
            }
            if ($request->user['imagem'] != null) {
                $user_img = $uploadImage->UploadImage('/login/image/',$request->user['imagem']);
            } else {
                $user_img = 'produto-sem-imagem.png';
            }

            $company = company::create([
                'nif' => $request->company['nif'],
                'phone' => $request->company['phone'],
                'name' => $request->company['name'],
                'activity_type_id' => $request->company['activity']['id'],
                'country' => $request->company['country']['name'],
                'image' => $company_img
            ]);

            $armagem = $company->armagens()->create([
                'name' => 'Principal'
            ]);

            $company->users()->create([
                'name' => $request->user['name'],
                'surname' => $request->user['name'],
                'email' => $request->user['email'],
                'phone' => $request->user['phone'],
                'password' => Hash::make($request->user['password']),
                'image' =>  $user_img,
                'armagen_id' => $armagem->id
            ]);

            $data = $company;

        });

        return $data;
    }

    public function ValidateLicense()
    {
        # code...
    }
    public function ValidateLicenseFree($license,$company,$user)
    {
       $license = $company->license()->create([
            'type_license_id' => 2,
            'from' => now(),
            'to' => Carbon::now()->addDays(15)
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

        return Redirect::route('welcome',['company'=>$company->id]);

    }

    public function welcome(company $company)
    {
        return Inertia::render('Welcome',[
            'company' => $company
        ]);
    }
}
