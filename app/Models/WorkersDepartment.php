<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkersDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id'
    ];

    public function workers():HasMany
    {
        return $this->hasMany(Workers::class,'workers_department_id');
    }
}
