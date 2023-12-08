<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class app extends Model
{
    use HasFactory;


    function appTranslate() : HasMany {
        return $this->hasMany(appsTranslate::class,'app_id');
    }
}
