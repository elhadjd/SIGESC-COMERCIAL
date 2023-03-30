<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'produtos_id',
        'price_sold',
        'TotalCost',
        'price_cost',
        'quantity',
        'total',
    ];

    protected $with = ['product'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(orderPos::class,'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(produtos::class,'produtos_id');
    }
}
