<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produtos extends Model
{
    use HasFactory;

    protected $with = [
        'category',
        'fornecedor',
        'product_type',
        'list_price',
        'stock'
    ];

    protected $fillable = [
        'company_id',
        'nome',
        'image',
        'codego',
        'category_product_id',
        'product_type_id',
        'fabricante',
        'preçocust',
        'imposto',
        'preçovenda',
        'preco_medio',
        'qtd',
        'total_cust',
        'inventory',
        'datafab',
        'dataexp',
        'estado',
    ];

    public function product_type()
    {
        return $this->belongsTo(productType::class,'product_type_id');
    }

    public function stock()
    {
        return $this->hasMany(stock::class);
    }

    public function type_movement()
    {
        return $this->belongsToMany(movement_type::class);
    }

    public function fornecedor()
    {
        return $this->belongsToMany(fornecedore::class);
    }

    public function category()
    {
        return $this->belongsTo(category_product::class,'category_product_id');
    }

    public function list_price()
    {
        return $this->hasMany(price_list::class);
    }

    public function catalogProduct(){
        return $this->hasMany(product_picture::class,'product_id');
    }
}
