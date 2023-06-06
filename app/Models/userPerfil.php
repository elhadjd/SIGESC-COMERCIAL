<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userPerfil extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'celular',
        'country',
        'address',
        'gender',
        'birthday',
    ];
}
