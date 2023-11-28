<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;
use App\Models\company;
use App\Models\EmailVerify;
use Illuminate\Support\Facades\Auth;

class emailVerifyController extends Controller
{
    function sendCodeVerifyEmail(company $model) {
        if($model->email != null || $model->email != ''){
            $code = 'SG';
            for ($i = 0; $i < 3; $i++) {
                $code = $code.rand(1, 100);
            }

            $model->emailVerify()->updateOrCreate(['company_id'=>$model->id],[
                'user_id'=>Auth::user()->id,
                'code'=>$code,
            ]);

            Mail::to($model->email)->send(new VerificationEmail($code,$model->name));

        }else{
            return $this->RespondError('O campo email esta vazio, por favor preenche e tenta novamente ');
        }
    }

    function validateCode(company $company,$code) {
        $verify = $company->emailVerify();
        if ($code == $verify->first()->code) {
            $verify->update(['isVerify'=>true]);
            return $this->RespondSuccess('Email verificado com sucesso',$company->emailVerify()->first());
        }
        return $this->RespondError('Codigo errado');
    }
}
