<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\company;
use App\Models\produtos;
use Illuminate\Http\Request;


class verifyInfoModelsController extends Controller
{
    function checkInfoCompany($company) {
        if (
             $company->description !=null &&
             $company->shopOnline == true &&
             $company->phone !=null &&
             $company->email !=null &&
             $company->city != null &&
             $company->country != null &&
             $company->longitude != null &&
             $company->sede != null &&
             $company->emailVerify()->first()
            ) {
                return true;
        }
        return 'false';
    }

    public function checkProductInfo($product) {
        if (
            $product->nome != null&&
            $product->preçovenda != null &&
            $product->image != null&&
            $product->estado != null&&
            $product->estado != 'inactive'&&
            $product->image != 'produto-sem-imagem.png' &&
            $product->description != null
            )
        {
            $category = $product->category()->first();
            $itemType = $product->product_type()->first();
            if ($category && $itemType) {
                return true;
            }
            return false;
        }
        return false;
    }
}
