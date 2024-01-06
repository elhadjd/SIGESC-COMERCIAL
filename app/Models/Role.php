<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;
    protected $with = ['translate'];

    function translate():HasMany {
        return $this->hasMany(roles_translate::class,'role_id');
    }
}

