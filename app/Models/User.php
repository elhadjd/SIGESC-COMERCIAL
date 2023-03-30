<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $with = ['config','armagen'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function config()
    {
        return $this->hasOne(userConfig::class);
    }

    public function company()
    {
        return $this->hasOne(company::class);
    }

    function armagen()
    {
        return $this->belongsTo(armagen::class);
    }

}
