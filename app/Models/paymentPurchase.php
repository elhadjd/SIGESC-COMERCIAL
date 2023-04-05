<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class paymentPurchase extends Model
{
    use HasFactory;

    protected $with = ['method'];

    protected $fillable = [
        'puchase_id',
        'payment_method_id',
        'Amount',
        'TotalPayments'
    ];

    public function method():BelongsTo
    {
        return $this->belongsTo(paymentMethod::class,'payment_method_id');
    }
}
