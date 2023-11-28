<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class company extends Model
{
    use HasFactory;

    protected $with = ['armagens','license','activity','manager','users','emailVerify'];

    protected $fillable = [
        'city',
        'email',
        'house_number',
        'image',
        'name',
        'nif',
        'phone',
        'sede',
        'country',
        'activity_type_id',
        'manager',
        'latitude',
        'longitude'
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
        return $this->hasMany(Workers::class);
    }

    function HistoricLogin():HasMany
    {
        return $this->hasMany(HistoricLogin::class);
    }

    public function purchase() : HasMany {
        return $this->hasMany(Puchase::class,'company_id');
    }

    public function invoice() : HasMany {
        return $this->hasMany(Invoice::class,'company_id');
    }

    public function transfer():HasMany {
        return $this->hasMany(transfer::class,'company_id');
    }

    public function products() : HasMany {
        return $this->hasMany(produtos::class,'company_id');
    }

    public function OrderPdv() : HasMany {
        return $this->hasMany(orderPos::class,'company_id');
    }

    public function DepartmentWorkers() : HasMany {
        return $this->hasMany(WorkersDepartment::class,'company_id');
    }

    function emailVerify() {
        return $this->hasOne(emailVerify::class,'company_id');
    }

}
