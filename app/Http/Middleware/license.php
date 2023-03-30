<?php

namespace App\Http\Middleware;

use App\classes\Data;
use App\Models\DataMuvementoModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class license
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $data = new Data();
        $VerifyDate = DB::table('data_muvemento_models')
            ->where('created_at', '>', now())->get();
        if ($VerifyDate->count() > 0) {
            return Inertia::render('license', [
                'license' => false
            ]);
        }

        // A verificar se existe uma licença ativa
        $SelectLicense = DB::table('Licensas')->where('updated_at', '>', now())->get();
        if ($SelectLicense->count() <= 0) {
            return Inertia::render('license', [
                'license' => true,
            ]);
        }

        // A verificar se a data dde muvementos enta preenchida
        $dataMuv = DB::table('data_muvemento_models')
            ->where('dia', date('d'))
            ->Where('mes', $data->Data()['mes'])
            ->Where('ano', date('Y'));
        // A verificar
        if ($dataMuv->count() <= 0) {
            $dataMuvemento = DataMuvementoModel::all();
            DB::table('data_muvemento_models')
                ->insert([
                    'dia' => date('d'),
                    'mes' => $data->Data()['mes'],
                    'ano' => date('Y')
                ]);
        } elseif ($dataMuv->count() > 1) {
            $dataMuv->orderBy('id', 'DESC')->delete();
        }
        return $next($request);
    }
}
