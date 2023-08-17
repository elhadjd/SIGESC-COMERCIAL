<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class caixa extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'company_id',
        'password',
        'state',
    ];

    protected $with = ['user'];

    public function session()
    {
        return $this->hasMany(session::class);
    }

    function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }
}
