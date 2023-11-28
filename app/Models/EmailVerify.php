<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerify extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'isVerify',
        'code'
    ];

    function company() {
        $this->belongsTo(company::class,'company_id');
    }
    
    use HasFactory;
}
