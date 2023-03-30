<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\fornecedore;
use App\Models\fornecedore_produtos;
use App\Models\produtos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class suppliersController extends Controller
{
    public function get()
    {
        return fornecedore::all();
    }

    public function create()
    {
        # code...
    }

    public function show(fornecedore $supplier)
    {
        return $supplier;
    }

    public function update()
    {
        # code...
    }

    public function delete()
    {
        # code...
    }

    public function AddProductSupplier(produtos $product, fornecedore $supplier)
    {
        $validate = DB::table('fornecedore_produtos')->where('produtos_id',$product->id)
        ->where('fornecedore_id',$supplier->id)->get();
        if ($validate->count() > 0) return $this->RespondError('Este fornecedore já foi adicionado neste produto');
        fornecedore_produtos::create([
            'produtos_id' => $product->id,
            'fornecedore_id' => $supplier->id
        ]);

        return $product->fresh();
    }

    public function deleteSupplierProduct(produtos $product,fornecedore $supplier,fornecedore_produtos $fornecedore_produtos)
    {
        $product->fornecedor()->detach($supplier->id);
        return $product->fresh();
    }
}
