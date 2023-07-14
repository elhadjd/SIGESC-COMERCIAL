<?php
namespace App\classes;

use App\Models\HistoricAtivitie;
use App\Models\HistoricLogin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class ActivityRegister
{

    public function Activity($body)
    {

        $user = User::find(Auth::user()->id);
        $finally_login = $user->historic_login()->orderBy('id', 'DESC')->first();
        $finally_login->activities()->create([
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->company_id,
            'body' => $body
        ]);
    }
}
