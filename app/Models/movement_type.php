<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class movement_type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function movements()
    {
        return $this->hasMany(movement_type_produtos::class,'movement_type_id');
    }
}
