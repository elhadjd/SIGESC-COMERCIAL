<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'company_id',
        'country',
        'email',
        'image',
        'name',
        'phone',
        'rua',
        'state',
        'surname',
        'whatsapp',
    ];

    public function invoices():HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
