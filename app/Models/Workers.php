<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workers extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'name',
        'cargo',
        'email',
        'phone',
        'user_id',
        'celular',
        'workers_department_id',
        'manager',
        'trainer',
        'hourWork',
        'salary',
        'dailyExpense'
    ];

    protected $with = ['manager','trainer','department','user','expenses'];

    public function manager()
    {
        return $this->belongsTo(User::class,'manager');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class,'trainer');
    }

    public function department()
    {
        return $this->belongsTo(WorkersDepartment::class,'workers_department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenses()
    {
        return $this->hasMany(expense::class,'worker_id');
    }

}
