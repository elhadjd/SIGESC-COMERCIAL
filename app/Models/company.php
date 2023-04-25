<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class company extends Model
{
    use HasFactory;

    protected $with = ['armagens','license','activity','manager','users'];

    protected $fillable = [
        'city',
        'email',
        'house_number',
        'image',
        'name',
        'nif',
        'phone',
        'sede'
    ];

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

    public function activity()
    {
        return $this->belongsTo(activity_type::class,'activity_type_id');
    }
    public function manager()
    {
       return $this->belongsTo(User::class,'manager');
    }
    public function workers()
    {
        return $this->hasMany(workers::class);
    }

}
