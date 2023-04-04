<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Puchase extends Model
{
    use HasFactory;

    protected $with = ['supplier','user'];

    protected $fillable = [
        'totalDiscount',
        'totalTax',
        'TotalSpending',
        'totalMerchandise',
        'total'
    ];

    public function supplier():BelongsTo
    {
        return $this->belongsTo(fornecedore::class,'fornecedor_id');
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items():HasMany
    {
        return $this->hasMany(PuchaseItem::class);
    }
}
