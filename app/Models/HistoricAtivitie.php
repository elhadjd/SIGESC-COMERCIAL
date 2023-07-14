<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricAtivitie extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'historic_login_id',
        'body'
    ];
}
