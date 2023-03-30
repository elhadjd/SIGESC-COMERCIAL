<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'armagen_id',
        'produtos_id',
        'quantity'
    ];

    protected $database = 'stocks';

    protected $with = ['armagen'];

    public function product()
    {
        return $this->belongsTo(produtos::class,'produtos_id','id');
    }

    public function armagen()
    {
        return $this->belongsTo(armagen::class);
    }

}
