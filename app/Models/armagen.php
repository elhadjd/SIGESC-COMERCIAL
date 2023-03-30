<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class armagen extends Model
{
    use HasFactory;

     protected $table = 'armagens';

    // protected $with = ['stock'];

    public function stock()
    {
        return $this->hasMany(stock::class,'armagen_id','id');
    }
}
