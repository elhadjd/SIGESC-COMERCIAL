<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'DateOrder',
        'orderNumber',
        'tax'
    ];

    protected $with = ['client','user'];

    public function client()
    {
        return $this->belongsTo(cliente::class,'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function payments()
    {
        return $this->hasMany(PaymentInvoice::class);
    }
}
