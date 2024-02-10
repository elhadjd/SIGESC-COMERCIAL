<?php

namespace App\Http\Controllers\Public;

use App\classes\CheckData;
use App\classes\uploadImage;
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
        return fornecedore::where('company_id',Auth::user()->company_id)->get();
    }

    public function create()
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Providers','Create')) return $this->RespondError(__('User without access'));
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

    public function update(fornecedore $supplier , Request $request)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Providers','Edit')) return $this->RespondError(__('User without access'));
        $image = new uploadImage();
        $request->validate([
            'name'=> 'required||string'
        ]);

        $data = $request->all();

        unset($data['id'],$data['products'],$data['orders'],$data['created_at'],$data['updated_at']);

        if($data['images']!= null) $data['image'] = $image->Upload('/supplier/image/',$request->images);

        if ($supplier->update($data)) return $this->RespondSuccess(__('Data updated successfully'),$this->relations($supplier));

        return $this->RespondError(__('A server error occurred'));
    }

    public function deleteSupplier(fornecedore $supplier)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Providers','Delete')) return $this->RespondError(__('User without access'));
        $relation = $this->relations($supplier);

        if ($relation->products->count() >0 || $relation->orders->count() >0) return $this->RespondError(__('It is not possible to delete this supplier'));
        if ($relation->delete()) return $this->RespondSuccess(__('Item deleted successfully'));
    }

    public function delete()
    {

    }

    public function AddProductSupplier(produtos $product, fornecedore $supplier)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Products','Create')) return $this->RespondError(__('User without access'));
        $validate = DB::table('fornecedore_produtos')->where('produtos_id',$product->id)
        ->where('fornecedore_id',$supplier->id)->get();
        if ($validate->count() > 0) return $this->RespondError(__('This supplier has already been added to this article'));
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
