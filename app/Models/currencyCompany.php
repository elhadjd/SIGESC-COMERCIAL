<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class currencyCompany extends Model
{
    use HasFactory;
    protected $table = 'currencyCompanies';
    protected $fillable = [
        'code',
        'digits',
        'number',
        'currency',
        'company_id',
        'createdAt',
        'updatedAt'
    ];
}
