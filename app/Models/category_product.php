<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category_product extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'name'
    ];

    public function products()
    {
        return $this->hasMany(produtos::class);
    }
}
