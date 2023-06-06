<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userConfig extends Model
{
    use HasFactory;
    protected $fillable = [
        'infoCompany',
        'price',
        'quantity',
        'print'
    ];
}
