<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistoricLogin extends Model
{
    use HasFactory;

    protected $with = ['activities','user'];

    protected $fillable = [
        'company_id',
        'user_id',
        'ip_address',
        'browser',
    ];

    function activities() : HasMany
    {
        return $this->hasMany(HistoricAtivitie::class);
    }

    function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
