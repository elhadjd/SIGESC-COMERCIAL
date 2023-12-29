<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class forgetPasswordController extends Controller
{
    function RequestData(Request $request,$locale) {
        app()->setLocale($locale);
        $request->validate([
            'email'=> ['required'],
            'name'=>['required'],
            'phone'=>['required'],
            'surname'=>['required']
        ]);
        $user = User::where('email',$request->email)->first();
        if ($user) {
            
            if($user->state == 'active' &&
            Str::of(str_replace(' ','',$user->name))->lower() == Str::of(str_replace(' ','',$request->name))->lower() && 
            Str::of(str_replace(' ','',$user->surname))->lower() == Str::of(str_replace(' ','',$request->surname))->lower() &&
            Str::of(str_replace(' ','',$user->perfil->phone)) == Str::of(str_replace(' ','',$request->phone))){
                $code = '';
                for ($i = 0; $i < 2; $i++) {
                    $code = $code.rand(1, 100);
                }
                Cookie::queue('resetPasswordCode',$code);
                return response()->json(['check'=>$code,'userId'=>$user->id]);
            }
            return $this->RespondWarn(__('data incorrect.'));
        }else{
            return $this->RespondError(__('User not found'));
        }
    }

    function resetPassword(Request $request,$locale) {
        app()->setLocale($locale);
        $request->validate([
            'password'=>'required|min:6|max:32',
            'passwordTwo'=>'required',
            'check'=>'required',
            'userId'=>'required'
        ]);
        $check = Cookie::get('resetPasswordCode');

        if($check != $request->check) return $this->RespondWarn('Date expired');

        if($request->password != $request->passwordTwo) return $this->RespondError(__('data incorrect.'));
        $user = User::find($request->userId);
        $password = Hash::make($request->password);
        $user->password = $password;
        if($user->save()) return $this->RespondSuccess(__('Data updated successfully'));
        return $this->RespondError(__('A server error occurred'));
    }
}
