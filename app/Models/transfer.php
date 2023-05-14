<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'store_principal_id',
        'store_destination_id',
        'total_spent',
        'state',
        'total'
    ];

    protected $with = ['user','store_from','store_to'];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function company():BelongsTo
    {
        return $this->belongsTo(company::class,'company_id');
    }

    public function store_from():BelongsTo
    {
        return $this->belongsTo(armagen::class,'store_principal_id');
    }

    public function store_to():BelongsTo
    {
        return $this->belongsTo(armagen::class,'store_destination_id');
    }

    public function items():HasMany
    {
        return $this->hasMany(transferItem::class);
    }

}
