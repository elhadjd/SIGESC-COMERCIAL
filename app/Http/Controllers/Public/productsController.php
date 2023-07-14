<?php

namespace App\Http\Controllers\Public;

use App\classes\ActivityRegister;
use App\classes\uploadImage;
use App\Http\Controllers\Controller;
use App\Models\category_product;
use App\Models\company;
use App\Models\fornecedore;
use App\Models\movement_type;
use App\Models\productType;
use Illuminate\Support\Str;
use App\Models\produtos;
use App\Models\stock;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class productsController extends Controller
{
    public function get_company()
    {
        return company::find(Auth::user()->id)->id;
    }

    public function get($page)
    {
        $product = produtos::withSum('stock','quantity')
        ->where('company_id',Auth::user()->company_id)
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

        return response()->json([
            'user'=> Auth::user(),
            'product' => $product->withSum('stock','quantity')->whereId($product->id)->first(),
            'type_movements' =>  $type_movements,
            'suppliers' => fornecedore::all(),
            'categorys' => category_product::all(),
            "product_type" => productType::all()
        ]);
    }

    public function update(produtos $product, Request $request)
    {
        $image = new uploadImage();
        $data = $request->produtos;
        unset($data['id'], $data['category'], $data['movement_stock'], $data['fornecedor'], $data['updated_at'], $data['created_at']);
        if (Auth::user()->nivel == 'admin') {

            if ($request->imagem != null) {
                $data['image'] = $image->Upload("/produtos/image/", $request->imagem, $product);
            }
            if ($product->update($data)) {
                $this->registerActivity("Atualizou os dados do produto $product->nome");
                return $this->RespondSuccess('Produto Atualizado com Sucesso',$product->fresh());
            } else {
                return $this->RespondError('Erro ao Adicionar o produto');
            }
        }else{
            if ($request->imagem != null) {
                $product->image = $image->Upload("/produtos/image/", $request->imagem, $product);
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

            return $this->RespondSuccess('Categoria adicionada com successo');
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
}
