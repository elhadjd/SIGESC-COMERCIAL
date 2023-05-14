<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Http\Controllers\public\imagensController;
use App\Models\activity_type;
use App\Models\company;
use App\Models\User;
use Illuminate\Http\Request;
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
        return company::find($request->user()->id)->load('users');
    }
    public function getConfig(Request $request)
    {
        return $request->user()->company()->first();
    }

    public function SaveUser(User $user = null,Request $request , imagensController $uploadImage)
    {
        $data = $request->all();
        unset($data['armagen'], $data['updated_at'], $data['id'], $data['config']);
        if ($data['image'] != $user->image) {
            $data['image'] = $uploadImage->UploadImage('/login/image/',$data['image'],$user);
        }
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
    public function saveCompany(Request $request, imagensController $uploadImage)
    {

        if($request->imagem) {
        $image = $uploadImage->UploadImage('/company/image/',$request->imagem);
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
