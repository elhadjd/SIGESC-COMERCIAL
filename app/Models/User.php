<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'armagen_id',
        'company_id',
        'image',
        'nivel',
        'state',
        'surname',
    ];

    protected $with = ['config','armagen','perfil'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function perfil(): HasOne
    {
        return $this->hasOne(userPerfil::class);
    }

    public function historic_login():HasMany {
        return $this->HasMany(HistoricLogin::class);
    }

    public function config(): HasOne
    {
        return $this->hasOne(userConfig::class);
    }

    public function company()
    {
        return $this->belongsTo(company::class);
    }

    function armagen()
    {
        return $this->belongsTo(armagen::class);
    }

    public function invoice():HasMany
    {
        return $this->hasMany(Invoice::class,'user_id');
    }

}
