<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fornecedore_produtos extends Model
{
    use HasFactory;
    protected $fillable = [
        'produtos_id',
        'fornecedore_id'
    ];
}
