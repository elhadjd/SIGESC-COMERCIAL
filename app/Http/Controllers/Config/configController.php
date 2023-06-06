<?php

namespace App\Http\Controllers\Config;

use App\classes\uploadImage;
use App\Http\Controllers\Controller;
use App\Models\activity_type;
use App\Models\company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class configController extends Controller
{
    public function index()
    {
        return Inertia::render('Config/index');
    }

    public function users(Request $request)
    {
        return company::find($request->user()->company_id)->load('users');
    }

    public function getConfig(Request $request)
    {
        return $request->user()->company()->first();
    }

    public function newUser(User $users)
    {
        $user = $users->create([
            'company_id'=>Auth::user()->company_id
        ]);
        $user->config()->create();
        $user->perfil()->create();
        return $user->fresh();
    }

    public function SaveUser(User $user = null,Request $request)
    {
        $image = new uploadImage();
        $data = $request->all();
        unset($data['updated_at'], $data['id'],$data['config']['id'],
        $data['perfil']['id'],$data['perfil']['created_at'],$data['perfil']['updated_at'],
        $data['config']['created_at'],$data['config']['updated_at']);
        if ($request->imagem && $request->imagem != $user->image) {
            $data['image'] = $image->Upload('/login/image/',$data['imagem'],$user);
        }
        if ($request->armagen) $data['armagen_id'] = $data['armagen']['id'];
        $user->update($data);
        $user->perfil()->update($data['perfil']);
        $user->config()->update($data['config']);
        return $this->RespondSuccess('Usuario atualizado com success !!!',$user->fresh());
    }

    public function UpdatePassword(Request $request,User $user)
    {
        if (!Hash::check($request->SenhaAtual,$user->password)) {
            return $this->RespondError('info','A senha atual esta incorreta');
        }
        elseif (Hash::check($request->NovaSenha,$user->password)) {
            return $this->RespondError('info','A nova senha não pode ser igual com a senha atual');
        }
        $user->password = Hash::make($request->NovaSenha);
        if ($user->save()) {
            return $this->RespondSuccess('success','Senha atualizada com sucesso');
        }
    }
    public function saveCompany(Request $request)
    {
        $image = new uploadImage();
        if($request->imagem) {
        $image = $image->Upload('/company/image/',$request->imagem);
        } else {
            $image = $request->image;
        }

        $request->user()->company()->update([
            'city' => $request->city,
            'email' => $request->email,
            'house_number' => $request->house_number,
            'image' => $image,
            'name' => $request->name,
            'nif' => $request->nif,
            'phone' => $request->phone,
            'sede' => $request->sede
        ]);

        return $this->respondSuccess('Dados atualizado com Sucesso');
    }

    public function getActivity()
    {
        return activity_type::all();
    }
}
