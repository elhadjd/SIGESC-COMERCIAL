<?php

namespace App\Http\Controllers\config;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\service;
use App\Models\User;

class permissionsController extends Controller
{
    function show(User $user) {
        if(!request()->user()->hasRole('Admin')) return $this->RespondError(__('User without access'));
        return service::with(['permissions'=>function($permission) use ($user){
            $permission->with(['permissionUser'=>function($permissionUser) use ($user){
                $permissionUser->where('user_id',$user->id);
            }]);
        }])
        ->with(['translate'=>function($translate){
            $local = app()->getLocale();
            $translate->where('local',$local);
        }])->get();
    }

    function savePermission(User $user) {
        if(!request()->user()->hasRole('Admin')) return $this->RespondError(__('User without access'),$this->show($user));
        foreach (request()->all() as $service) {
            foreach ($service['permissions'] as $permission) {
                $permissionNew = Permission::find($permission['id']);
                if($permission['state']){
                    $user->assignPermission($permissionNew);
                }else{
                    if($user->hasPermission($permissionNew->id)){
                        $user->unSignPermission($permissionNew);
                    }
                }
            }
        }

        return $this->RespondSuccess(__('Data updated successfully'),["services"=>$this->show($user),"user"=>request()->user()]);
    }
}
