<?php
namespace App\classes;

use App\Models\service;

class CheckData
{
    public function checkPermission(string $columnData ,string $permissionName){
        $service = service::where('name',$columnData)->with(['permissions'=>function($permission) use ($permissionName){
            $permission->where('name',$permissionName);
        }])->first();
        if(request()->user()->hasRole('Admin')) return true;
        
        return request()->user()->hasPermission($service->permissions[0]['id']);
    }
}