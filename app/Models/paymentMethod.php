<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class paymentMethod extends Model
{
    use HasFactory;

    public function paymentsPdv(): HasMany
    {
        return $this->hasMany(paymentPDV::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentInvoice::class);
    }
}
