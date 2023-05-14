<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'produtos_id',
        'invoice_id',
        'quantity' ,
        'PriceCost' ,
        'PriceSold',
        'TotalDiscount',
        'Discount',
        'TotalCost',
        'TotalSold',
        'final_price',
        'tax',
        'totalTax',
        'armagen_id'
    ];

    protected $with = ['product','store'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(armagen::class,'armagen_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(produtos::class,'produtos_id');
    }
}
