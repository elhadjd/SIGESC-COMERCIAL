<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class price_list extends Model
{
    use HasFactory;

    protected $fillable = [
        'produtos_id',
        'company_id',
        'user_id',
        'quantity',
        'price_discount'
    ];
}
