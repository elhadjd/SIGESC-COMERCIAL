<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class session extends Model
{
    use HasFactory;

    protected $fillable = [
        'caixa_id',
        'user_id',
        'state'
    ];

    protected $database = 'sessions';

    protected $with = ['user','caixa'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function orders()
    {
        return $this->hasMany(orderPos::class);
    }

    public function operation_type()
    {
        return $this->belongsToMany(operationCaixaType::class);
    }

    public function entry_output()
    {
        return $this->hasMany(entryOutput::class);
    }

    public function caixa():BelongsTo
    {
        return $this->belongsTo(caixa::class);
    }
}
