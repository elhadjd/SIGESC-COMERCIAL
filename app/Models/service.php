<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class service extends Model
{
    use HasFactory;

    function permissions(): HasMany {
        return $this->hasMany(Permission::class,'service_id');
    }

    function translate() : HasMany {
        return $this->hasMany(serviceTranslate::class,'service_id');
    }
}
