<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class operation_caixa_type_session extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'session_id',
        'operation_caixa_type_id',
        'amount',
        'subject'
    ];

    protected $with = ['user'];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function operations()
    {
        return $this->belongsTo(operationCaixaType::class,'operation_caixa_type_id');
    }
}
