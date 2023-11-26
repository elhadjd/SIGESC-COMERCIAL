<?php

namespace App\Http\Controllers\Public;

use App\classes\ActivityRegister;
use App\classes\DeleteFile;
use App\classes\uploadImage;
use App\classes\UploadImageCatalog;
use App\Http\Controllers\Controller;
use App\Models\category_product;
use App\Models\company;
use App\Models\fornecedore;
use App\Models\movement_type;
use App\Models\product_picture;
use App\Models\productType;
use App\Models\produtos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class productsController extends Controller
{
    public function get_company()
    {
        return company::find(Auth::user()->id)->id;
    }

    public function get($page)
    {
        $product = produtos::withSum(['stock' => function($stock){
            $stock->where('armagen_id',Auth::user()->armagen_id);
        }],'quantity')->where('company_id',Auth::user()->company_id)
        ->where('estado','active')->orderBy('nome','asc')->paginate($page);
        // foreach ($product as $value) {
        //    $select = DB::table('stocks')->where('armagen_id',Auth::user()->armagen_id)
        //     ->where('produtos_id',$value->id);
        //     if ($select->count() >1) {
        //        $product = $select->orderBy('id','DESC');
        //     }
        // }
        return $product;


        // foreach ($product as $value) {
        //     stock::create([
        //         'armagen_id'=>Auth::user()->armagen_id,
        //         'produtos_id' => $value['id'],
        //         'quantity'=>$value['qtd'] == null ? 0: $value['qtd']
        //     ]);
        // }
    }

    public function create(produtos $produtos,$name=null)
    {

        if (Auth::user()->nivel == 'admin') {
            $product = $produtos::create([
                'nome'=> $name,
                'company_id' => Auth::user()->company_id
            ]);
            $this->registerActivity('Criou um produto');
            return $this->show(produtos::find($product->id));
        }

        return $this->RespondWarn('Usuario sem acesso !!!');
    }

    public function registerActivity($body)
    {
        $register = new ActivityRegister;
        $register->Activity($body);
    }

    public function show(produtos $product)
    {
        $type_movements = $product->type_movement()->first();
        if ($type_movements !== null) {
           $type_movements = $product->type_movement()->first()->with(['movements' => function($query) use ($product){
                $query->where('produtos_id',$product->id);
            }])->get();
        }else{
            $type_movements = movement_type::all();
        }

        $prod = $product
        ->with('catalogProduct')
        ->withSum(['stock' => function($stock){
            $stock->where('armagen_id',Auth::user()->armagen_id);
        }],'quantity')->whereId($product->id)->first();

        return response()->json([
            'user'=> Auth::user(),
            'product' => $prod,
            'type_movements' =>  $type_movements,
            'suppliers' => fornecedore::all(),
            'categorys' => category_product::all(),
            "product_type" => productType::all()
        ]);
    }

    public function update(produtos $product, Request $request)
    {
        $image = new uploadImage();
        $data = $request->data;
        unset($data['id'], $data['category'], $data['movement_stock'], $data['fornecedor'], $data['updated_at'], $data['created_at']);
        if (Auth::user()->nivel == 'admin') {

            if ($data['imagem'] != null) {
                $data['image'] = $image->Upload("/produtos/image/", $data['imagem'], $product);
            }
            if ($product->update($data)) {
                $this->registerActivity("Atualizou os dados do produto $product->nome");
                return $this->RespondSuccess('Produto Atualizado com Sucesso',$product->fresh());
            } else {
                return $this->RespondError('Erro ao Adicionar o produto');
            }
        }else{
            if ($data['imagem'] != null) {
                $product->image = $image->Upload("/produtos/image/", $data['imagem'], $product);
                $product->save();
                return $this->RespondSuccess('Imagem Atualizada com Sucesso',$product->fresh());
            }else{
                return $this->RespondWarn('Usuario sem acesso');
            }
        }
    }

    public function addCategoryProduct(produtos $product,Request $request,$category_product = null)
    {
        if (!$category_product) {
            $create = category_product::create([
                'name' => $request->name
            ]);
            produtos::find($product->id)->update([
                'category_product_id' => $create->id
            ]);

            return $this->RespondSuccess('Categoria adicionada com successo',$create);
        }
    }

    public function getProducts()
    {
        return produtos::all();
    }

    public function deleteProduct(produtos $product)
    {
        $product->estado = 'inactive';
        $product->save();
    }

    public function uploadImageCatalog(Request $request,produtos $product){
        $request->validate([
            'image' => 'required',
        ]);
        if (Auth::user()->nivel != 'admin') {
            return $this->RespondInfo('Usuário sem acesso');
        }

        $countImage = $product->catalogProduct->count();
        if ($countImage>=5) return $this->RespondInfo('Um produto so pode ter no maximo 5 image !!!',$product->load('catalogProduct'));
        $imageUploader = new UploadImageCatalog();
        $nameImage = $imageUploader->upload($product->id, $request->image);

        product_picture::create([
            'product_id'=>$product->id,
            'image'=>$nameImage
        ]);

        $product->fresh();

        $product->load('catalogProduct');

        return $product;
    }

    public function deleteImageInCatalog(product_picture $image){
        if (!$image) return $this->RespondError('Image ja não existe');
        $product = produtos::find($image->product_id);

        $deleteFile = new DeleteFile();
        
        $deleteFile->delete("/produtos/image/$product->id/$image->image");

        $image->delete();

        return $product->load('catalogProduct');

    }
}
