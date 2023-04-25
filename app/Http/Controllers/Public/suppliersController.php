<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\fornecedore;
use App\Models\fornecedore_produtos;
use App\Models\produtos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Object_;

class suppliersController extends Controller
{
    public function get()
    {
        return fornecedore::all();
    }

    public function create()
    {
        $new = fornecedore::create([
            'company_id' => Auth::user()->company_id,
            'image'=> 'produto-sem-imagem.png'
        ]);

        return $this->relations($new);
    }

    public function show(fornecedore $supplier)
    {
        return $supplier;
    }

    public function update(fornecedore $supplier , Request $request,imagensController $uploadImage)
    {
        $request->validate([
            'name'=> 'required||string'
        ]);

        $data = $request->all();

        unset($data['id'],$data['products'],$data['orders'],$data['created_at'],$data['updated_at']);

        if($data['images']!= null) $data['image'] = $uploadImage->UploadImage('/supplier/image/',$request->images);

        if ($supplier->update($data)) return $this->RespondSuccess('Sucesso',$this->relations($supplier));
        
        return $this->RespondError('Erro ao salvar fornecedore');
    }

    public function deleteSupplier(fornecedore $supplier)
    {
        $relation = $this->relations($supplier);
        
        if ($relation->products->count() >0 || $relation->orders->count() >0) return $this->RespondError('Não é posivel apagar este fornecedor');
        if ($relation->delete()) return $this->RespondSuccess('Fornecedor eliminado com sucesso');
    }

    public function delete()
    {
        
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

    public function relations(fornecedore $supplier)
    {
        $supplier->load('products');
        $supplier->load('orders');
        return $supplier;
    }
}
