<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class movement_type extends Model
{
    use HasFactory;

    protected $with = ['movementTranslate'];

    protected $fillable = [
        'name'
    ];

    public function movements()
    {
        return $this->hasMany(movement_type_produtos::class,'movement_type_id');
    }

    function movementTranslate() : HasMany {
        return $this->hasMany(typeMovementTranslate::class,'type_movement_id');
    }
}
