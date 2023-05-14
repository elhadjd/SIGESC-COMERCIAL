<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class transferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'produtos_id',
        'quantity',
        'priceSold',
        'spent',
        'final_price',
        'total',
        'armagen_id'
    ];

    protected $with = ['product','store'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(armagen::class,'armagen_id');
    }

    public function product():BelongsTo
    {
        return $this->belongsTo(produtos::class,'produtos_id');
    }
}
