<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class fornecedore extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'country',
        'email',
        'image',
        'name',
        'nif',
        'phone',
        'sede',
        'city'
    ];

    public function products()
    {
        return $this->belongsToMany(produtos::class);
    }

    public function orders():HasMany
    {
        return $this->hasMany(Puchase::class,'fornecedor_id');
    }

}
