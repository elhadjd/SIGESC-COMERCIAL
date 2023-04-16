<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class orderPos extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id' ,
        'armagen_id',
        'total',
        'discount' ,
        'total_discount',
        'cliente',
        'state',
        'number',
        'total_costs'
    ];

    protected $with = ['user','items','payments'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(paymentPDV::class);
    }

    public function items():HasMany
    {
        return $this->hasMany(ItemOrder::class,'order_id');
    }

    public function movement(): HasMany
    {
        return $this->hasMany(movement_type_produtos::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(session::class);
    }
}
