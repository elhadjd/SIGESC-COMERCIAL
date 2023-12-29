<?php

namespace App\Http\Controllers\Public;

use App\classes\ActivityRegister;
use App\classes\DeleteFile;
use App\classes\uploadImage;
use App\classes\UploadImageCatalog;
use App\Http\Controllers\Controller;
use App\Http\Controllers\public\verifyInfoModelsController;
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

    public function create(Request $request, produtos $produtos,$name=null)
    {

        if ($request->user()->hasRole('Admin')) {
            $product = $produtos::create([
                'nome'=> $name,
                'company_id' => Auth::user()->company_id
            ]);
            $this->registerActivity('Criou um produto');
            return $this->show(produtos::find($product->id));
        }

        return $this->RespondWarn(__('User without access'));
    }

    public function registerActivity($body)
    {
        $register = new ActivityRegister;
        $register->Activity($body);
    }

    public function show(produtos $product)
    {
        $language = Auth::user()->userLanguage;
        if ($language) {
            $locale = $language->code;
        }else{
            $locale = 'en';
        }
        $type_movements = $product->type_movement()->with(['movementTranslate'=>function($translate) use ($locale){
            $translate->where('local', $locale);
        }])->first();
        if ($type_movements !== null) {

            $type_movements = $product->type_movement()->first()
            ->with(['movementTranslate'=>function($translate) use ($locale){
                $translate->where('local', $locale);
            }])
            ->with(['movements' => function($movement) use ($product,$locale){
                $movement->where('produtos_id',$product->id)
                ->with(['movementTranslate'=>function($translate) use ($locale){
                    $translate->where('local', $locale);
                }]);
            }])->get();

        }else{
            $type_movements = movement_type::with(['movementTranslate'=>function($translate) use($locale){
                $translate->where('local', $locale);
            }])->get();
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
        $request->validate([
            'data.preçocust'=> ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'data.preçovenda'=> ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'data.nome'=> "required|string",
        ]);
        $data = $request->data;
        unset($data['id'], $data['category'], $data['movement_stock'], $data['fornecedor'], $data['updated_at'], $data['created_at']);
        if ($request->user()->hasRole('Admin')) {

            if ($data['imagem'] != null) {
                $data['image'] = $image->Upload("/produtos/image/", $data['imagem'], $product);
            }
            if ($product->update($data)) {
                $this->registerActivity("Atualizou os dados do produto $product->nome");
                return $this->RespondSuccess(__('Data updated successfully'),$product->fresh());
            } else {
                return $this->RespondError(__('A server error occurred'));
            }
        }else{
            if ($data['imagem'] != null) {
                $product->image = $image->Upload("/produtos/image/", $data['imagem'], $product);
                $product->save();
                return $this->RespondSuccess(__('Data updated successfully'),$product->fresh());
            }else{
                return $this->RespondWarn(__('User without access'));
            }
        }
    }

    public function addCategoryProduct(produtos $product,Request $request,$category_product = null)
    {
        if (!$category_product) {
            $create = category_product::create([
                'image'=>'produto-sem-imagem.png',
                'name' => $request->name
            ]);
            produtos::find($product->id)->update([
                'category_product_id' => $create->id
            ]);

            return $this->RespondSuccess(__('Category added successfully'),$create);
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
        if (!$request->user()->hasRole('Admin')) {
            return $this->RespondInfo(__('User without access'));
        }

        $countImage = $product->catalogProduct->count();
        if ($countImage>=5) return $this->RespondInfo(__('A product can only have a maximum of 5 images'),$product->load('catalogProduct'));
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
        if (!$image) return $this->RespondError('unavailable');
        $product = produtos::find($image->product_id);

        $deleteFile = new DeleteFile();

        $deleteFile->delete("/produtos/image/$product->id/$image->image");

        $image->delete();

        return $product->load('catalogProduct');
    }

    public function publishProduct(produtos $product) {
        $company = company::find($product->company_id);
        $connected = @fsockopen("www.example.com", 80);
        if ($connected){
            $verifyInfoModel = new verifyInfoModelsController();
            $check = $verifyInfoModel->checkInfoCompany($company);
            if (!$check) return $this->RespondError(__('Please complete the steps in the company form'));
            if (!$company->emailVerify()->first()) return $this->RespondError(__('To publish your products online you must verify your email. Go to the configuration module, click on the company profile, then click on verify email'));

            $checkProduct = $verifyInfoModel->checkProductInfo($product);
            if ($checkProduct) {
                $countImage = $product->catalogProduct->count();
                if($countImage<3) return  $this->RespondInfo(__('Please add at least 3 photographs of the product, preferably a white background and a very clean image to better highlight the product'));
                $product->shop_online = true;
                $product->save();
                return $this->RespondSuccess(__('Your product is now available in the online store'),$product->fresh());
            }
            return $this->RespondError(__('Please fill in the empty fields and try again'));
        }else{
            return $this->RespondError(__('It appears that you do not have active internet, please check and try again'));
        }
    }
}
