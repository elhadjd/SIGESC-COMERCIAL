<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'TotalSold'
    ];

    protected $with = ['product'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(produtos::class,'produtos_id');
    }
}
