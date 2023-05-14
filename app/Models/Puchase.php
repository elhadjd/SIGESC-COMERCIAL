<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Puchase extends Model
{
    use HasFactory;

    protected $with = ['supplier','user','armagen'];

    protected $fillable = [
        'user_id',
        'totalDiscount',
        'totalTax',
        'TotalSpending',
        'totalMerchandise',
        'total',
        'fornecedor_id'
    ];

    public function supplier():BelongsTo
    {
        return $this->belongsTo(fornecedore::class,'fornecedor_id');
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function armagen(): BelongsTo
    {
        return $this->belongsTo(armagen::class);
    }

    public function items():HasMany
    {
        return $this->hasMany(PuchaseItem::class);
    }

    public function payments():HasMany
    {
        return $this->hasMany(paymentPurchase::class);
    }
}
