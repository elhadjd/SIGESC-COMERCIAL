<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class operationCaixaType extends Model
{
    use HasFactory;

    protected $with = ['operations'];

    public function operations()
    {
        return $this->hasMany(operation_caixa_type_session::class,'operation_caixa_type_id');
    }

    function operationTranslate() : HasMany {
        return $this->hasMany(operationCaixaTranslate::class,'operation_id');
    }
}
