<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuchaseItem extends Model
{
    use HasFactory;

    protected $with = ['product','store'];

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
        'totalTax',
        'armagen_id'
    ];

    public function product()
    {
        return $this->belongsTo(produtos::class,'produtos_id');
    }
    public function store()
    {
        return $this->belongsTo(armagen::class,'armagen_id');
    }
}
