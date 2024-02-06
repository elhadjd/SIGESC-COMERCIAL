<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        "discount",
        "startDate",
        "endDate"
    ];

    function product() : BelongsTo {
        return $this->belongsTo(produtos::class,"product_id");
    }
}
