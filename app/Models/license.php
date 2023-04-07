<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class license extends Model
{
    use HasFactory;

    protected $with = ['app_license'];

    protected $fillable = [
         'company_id',
         'type_license_id',
         'from',
         'to'
    ];

    public function type_license()
    {
        return $this->belongsTo(type_license::class);
    }

    public function app_license()
    {
        return $this->hasMany(app_license::class);
    }
}
