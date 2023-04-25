<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class expense extends Model
{
    
    protected $fillable = [
        'worker_id',
        'type',
        'value',
        'numberInvoice'
    ];

    use HasFactory;
}
