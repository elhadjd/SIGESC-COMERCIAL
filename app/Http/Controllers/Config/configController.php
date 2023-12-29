<?php

namespace App\Http\Controllers\Config;

use App\classes\ActivityRegister;
use App\classes\uploadImage;
use App\Http\Controllers\Controller;
use App\Models\activity_type;
use App\Models\company;
use App\Models\currencyCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Models\EmailVerify;
use App\Models\Role;

class configController extends Controller
{
    public function index()
    {
        $this->registerActivity('Entrou no module configuração');
        return Inertia::render('Config/index');
    }

    function getRoles() {
       return Role::get();
    }

    public function users(Request $request)
    {
        return company::find($request->user()->company_id)->load('users');
    }

    function getLoginRegister()
    {
        $company = company::find(Auth::user()->company_id);
        return $company->HistoricLogin()->get();
    }


    public function getConfig(Request $request,$locale)
    {
        return $request->user()->company()
            ->with(['license'=>function($license) use ($locale){
                $license->with(['app_license'=>function($app_license) use ($locale){
                    $app_license->with(['apps'=>function($apps) use ($locale){
                        $apps->with(['appTranslate'=>function($translate) use ($locale){
                            $translate->where('local',$locale);
                        }]);
                    }]);
                }]);
        }])->first();
    }

    public function registerActivity($body)
    {
        $register = new ActivityRegister;
        $register->Activity($body);
    }

    public function newUser(User $users)
    {
        $this->registerActivity('Criou um novo usuario');
        $user = $users->create([
            'role_id'=>2,
            'company_id'=>Auth::user()->company_id,
            'image'=>'user-286.png'
        ]);
        $user->config()->create();
        $user->perfil()->create();
        $user->userLanguage()->create([
            'language'=>'Portugaise',
            'code'=>'pt',
        ]);
        return $user->fresh();
    }

    public function SaveUser(User $user = null,Request $request)
    {
        $request->validate([
            'email'=>['required','email'],
            'name'=>['required'],
            'surname'=>['required'],
            'roles'=>['required'],
            'armagen_id'=>['required'],
        ]);
        $image = new uploadImage();
        $data = $request->all();
        unset($data['updated_at'], $data['id'],$data['config']['id'],
        $data['perfil']['id'],$data['perfil']['created_at'],$data['perfil']['updated_at'],
        $data['config']['created_at'],$data['config']['updated_at']);
        if ($request->imagem && $request->imagem != $user->image) {
            $data['image'] = $image->Upload('/login/image/',$data['imagem'],$user);
        }
        if($data['senha1'] != null){
            $password = Hash::make($data['senha1']);
            $user->password = $password;
        }
        if ($request->armagen) $data['armagen_id'] = $data['armagen']['id'];
        $data['role_id'] = $data['roles']['id'];
        $user->update($data);
        $user->perfil()->update($data['perfil']);
        $user->config()->update($data['config']);
        $user->fresh();
        $this->insertLangUser($user,$data['user_language']);
        $this->registerActivity("Atualizou dados do usuario $user->name");

        return $this->RespondSuccess(__('Data updated successfully'),$user->fresh());
    }

    function insertLangUser($user,$data)  {
        $user->userLanguage()->updateOrCreate(['user_id'=>$user->id],$data);
    }

    public function UpdatePassword(Request $request,User $user)
    {
        if (!Hash::check($request->SenhaAtual,$user->password)) {
            return $this->RespondError(__('The provided password does not match your current password.'));
        }
        elseif (Hash::check($request->NovaSenha,$user->password)) {
            return $this->RespondError(__('The new password cannot be the same as the current password'));
        }
        $user->password = Hash::make($request->NovaSenha);
        if ($user->save()) {
            $this->registerActivity("Atualizou a senha do usuario $user->name");
            return $this->RespondSuccess(__('Data updated successfully'));
        }
    }
    public function saveCompany(Request $request)
    {

        $request->validate([
            'manager.id'=>['required'],
            'email'=>['required','email'],
            'phone'=>['required'],
            'nif'=>['required'],
            'city'=>['required'],
            'currency_company.code'=>['required'],
        ]);
        $image = new uploadImage();
        if($request->imagem) {
        $image = $image->Upload('/company/image/',$request->imagem);
        } else {
            $image = $request->image;
        }

        if($request->email != $request->user()->company()->first()->email) {
            if (EmailVerify::where('company_id',$request->id)) {
                EmailVerify::where('company_id',$request->id)->delete();
            };
        }

        $request->user()->company()->update([
            'city' => $request->city,
            'email' => $request->email,
            'house_number' => $request->house_number,
            'image' => $image,
            'name' => $request->name,
            'nif' => $request->nif,
            'phone' => $request->phone,
            'sede' => $request->sede,
            'manager'=>$request->manager['id'],
            'longitude'=>$request->longitude,
            'latitude'=>$request->latitude
        ]);

        currencyCompany::updateOrCreate(['company_id'=>$request->id],[
            'code'=>$request->currency_company['code'],
            'digits'=>$request->currency_company['digits'],
            'number'=>$request->currency_company['number'],
            'currency'=>$request->currency_company['currency'],
            'company_id'=>$request->id,
            'createdAt'=>now(),
            'updatedAt'=>now()
        ]);

        $this->registerActivity("Atualizou dados da empresa");
        return $this->respondSuccess(__('Data updated successfully'));
    }

    public function getActivity()
    {
        return activity_type::all();
    }

}
