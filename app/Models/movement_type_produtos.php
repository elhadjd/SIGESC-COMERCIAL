<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class movement_type_produtos extends Model
{
    use HasFactory;

    protected $table = 'movement_type_produtos';

    protected $fillable = [
        'user_id',
        'company_id',
        'produtos_id',
        'order_pos_id',
        'movement_type_id',
        'armagen_id',
        'quantity',
        'price_cost',
        'price_sold',
        'motive',
        'quantityAfter'
    ];


    public function product()
    {
        return $this->belongsTo(produtos::class,'produtos_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movement_type()
    {
        return $this->belongsTo(movement_type::class,'movement_type_id');
    }

    public function armagen()
    {
        return $this->belongsTo(armagen::class);
    }
    function movementTranslate() : HasMany {
        return $this->hasMany(typeMovementTranslate::class,'type_movement_id');
    }
}
