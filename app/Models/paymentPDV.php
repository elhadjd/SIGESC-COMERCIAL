<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class paymentPDV extends Model
{
    use HasFactory;

    protected $with = ['method'];

    protected $fillable = [
        'session_id',
        'order_pos_id',
        'payment_method_id',
        'amountPaid',
        'change',
    ];

    public function method():BelongsTo
    {
        return $this->belongsTo(paymentMethod::class,'payment_method_id');
    }

}
