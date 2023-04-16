<?php

namespace App\Http\Controllers\public;

use App\Http\Controllers\Controller;
use App\Models\armagen;
use App\Models\category_product;
use App\Models\company;
use App\Models\fornecedore;
use App\Models\movement_type_produtos;
use App\Models\productType;
use App\Models\produtos;
use App\Models\stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class stockController extends Controller
{

    public function get(company $company)
    {
        return $company::find(Auth::user()->company_id)->with(['armagens' => function($armagen) {
            $armagen->with('stock')
            ->get();
        }])->first();
    }

    public function GetAgroup()
    {
        return [
            'supplier' => fornecedore::all(),
            'category' => category_product::all(),
            'type_product' => productType::all()
        ];
    }


    public function getProductAgroup($type, $id, category_product $category, fornecedore $fornecedore, productType $productType)
    {
        if ($type === 'supplier') {
            return $fornecedore->with(['products' => function ($query) {
                $query->withSum('stock', 'quantity');
            }])->where('id', $id)->first();
        } else if ($type === 'category') {
            return $category->with(['products' => function ($query) {
                $query->orderBy('id', 'ASC')
                    ->withSum('stock', 'quantity');
            }])->where('id', $id)->first();
        } else {
            return $productType->with(['products' => function ($query) {
                $query->withSum('stock', 'quantity');
            }])->where('id', $id)->first();
        }
    }

    public function update(produtos $product, Request $request, stock $stock)
    {
        $consult = $stock->where('produtos_id', $product->id)->where('armagen_id', $request->armagen_id);
        if ($request->movement_type['name'] === "Entrada") {
            return $this->entryProduct($request, $product, $stock, $consult);
        } elseif ($request->movement_type['name'] === "Saida") {
            return $this->outputProduct($request, $product, $stock, $consult);
        }
    }

    public function entryProduct($request, $product, stock $stock, $consult)
    {
        if ($consult->count() > 0) {
            $quantityAfter = $consult->first()->quantity;
            $consult->update([
                'quantity' => $consult->first()->quantity + $request->quantity
            ]);
        } else {
            $quantityAfter = 0;
            $consult->create([
                'produtos_id' => $product->id,
                'armagen_id' => $request->armagen_id,
                'quantity' => $request->quantity
            ]);
        }

        return $this->createMovement($request, $product, $quantityAfter);
    }

    public function outputProduct($request, $product, stock $stock, $consult)
    {
        if ($consult->count() > 0) {
            $quantityAfter = $consult->first()->quantity;
            if ($consult->first()->quantity >= $request->quantity) {
                $consult->update([
                    'quantity' => $consult->first()->quantity - $request->quantity
                ]);
                return $this->createMovement($request, $product, $quantityAfter);
            }

            return $this->RespondError('Atencão a quantidade para retirar não pode ser maior do que a quantidade real !!!');
        }
        return $this->RespondError('Este produto não tem stock nesta armagen selectionado !!!');
    }

    public function createMovement($request, $product, $quantityAfter)
    {
        movement_type_produtos::create([
            'user_id' => Auth::user()->id,
            'produtos_id' => $product->id,
            'movement_type_id' => $request->movement_type['id'],
            'armagen_id' => $request->armagen_id,
            'quantity' => $request->quantity,
            'price_cost' => $product->preçocust,
            'price_sold' => $product->preçovenda,
            'motive' => $request->motive,
            'quantityAfter' => $quantityAfter
        ]);
    }


    public function destroy(string $id)
    {
    }
}
