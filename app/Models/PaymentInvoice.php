<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method_id',
        'Amount',
        'TotalPayments'
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(paymentMethod::class,'payment_method_id');
    }
}
