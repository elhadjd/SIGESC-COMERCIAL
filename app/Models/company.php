<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class company extends Model
{
    use HasFactory;

    protected $with = ['armagens','license'];

    public function users()
    {
        return $this->HasMany(User::class);
    }

    public function license()
    {
        return $this->hasOne(license::class);
    }

    public function apps()
    {
        return $this->HasMany(app::class);
    }

    public function caixas()
    {
        return $this->HasMany(caixa::class);
    }

    public function armagens()
    {
       return $this->hasMany(armagen::class);
    }

}
