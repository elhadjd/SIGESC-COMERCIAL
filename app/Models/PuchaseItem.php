<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuchaseItem extends Model
{
    use HasFactory;

    protected $with = ['product'];

    protected $fillable = [
        'puchase_id',
        'produtos_id',
        'quantity',
        'priceCost',
        'discount',
        'totalDiscount',
        'finalPrice',
        'totalItem',
        'spent',
        'priceSold',
        'tax',
        'totalTax'
    ];

    public function product()
    {
        return $this->belongsTo(produtos::class,'produtos_id');
    }
}
