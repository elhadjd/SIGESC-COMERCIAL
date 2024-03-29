<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class app_license extends Model
{
    use HasFactory;

    protected $with = ['apps'];

    protected $fillable = [
        'app_id',
        'license_id',
        'company_id'
    ];

    public function license()
    {
        return $this->belongsTo(license::class);
    }


    public function apps():BelongsTo
    {
        return $this->belongsTo(app::class,'app_id');
    }
}
