<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class fornecedore extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany(produtos::class);
    }
}