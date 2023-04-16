<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class configController extends Controller
{
    public function index()
    {
        return Inertia::render('Config');
    }
    public function users(Request $request)
    {
        return company::find($request->user()->id)->load('users');
    }
    public function getConfig(Request $request)
    {
        return company::find($request->user()->id)
        ->load('license')->load('users');
    }

    public function SaveUser(User $user = null,Request $request)
    {
        $data = $request->all();
        unset($data['armagen'],$data['updated_at'],$data['id'],$data['config']);
        $user->update($data);
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
}
