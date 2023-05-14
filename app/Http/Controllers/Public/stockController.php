<?php

namespace App\Http\Controllers\public;

use App\Http\Controllers\Controller;
use App\Models\armagen;
use App\Models\category_product;
use App\Models\company;
use App\Models\fornecedore;
use App\Models\movement_type_produtos;
use App\Models\operationCaixaType;
use App\Models\productType;
use App\Models\produtos;
use App\Models\stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class stockController extends Controller
{

    public function Index()
    {
        return Inertia::render('Stock/index');
    }

    public function get(Request $request)
    {
        return $request->user()->company()->with(['armagens' => function ($armagen) {
            $armagen->with('stock')->get();
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

    public function getRelatorProductsByMonth($month, $year)
    {
        $select = movement_type_produtos::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)->with('movement_type')->get();
        $spent = operationCaixaType::where('name', 'Gasto')->first();
        return [
            'spent' => $spent,
            'products' => produtos::withSum('stock', 'quantity')->where('estado', 'active')->get(),
            'list' => $select,
        ];
    }

    public function IntervalDateInventory($month = null, $year = null, $from, $to)
    {
        $startDate = Carbon::parse("$year-$month-$from");
        $endDate = Carbon::parse("$year-$month-$to");

        $endDate = date('Y-m-d H:i:s', strtotime($endDate . ' +1 day -1 second'));

        $select = movement_type_produtos::whereBetween('created_at', [$startDate, $endDate])
            ->with('movement_type')->get();

        $spent = operationCaixaType::where('name', 'Gasto')
            ->with(['operations' => function ($operations) use ($startDate, $endDate) {
                $operations->whereBetween('created_at', [$startDate, $endDate]);
            }])->first();

        return [
            'spent' => $spent,
            'products' => produtos::withSum('stock', 'quantity')->where('estado', 'active')->get(),
            'list' => $select,
        ];
    }
    public function getCatalog()
    {
        return produtos::all();
    }

    public function saveStore(Request $request)
    {
        $request->validate([
            'city' =>  'required',
            'name' => 'required'
        ]);
       
        armagen::updateOrCreate(
            ['name' => $request->name,'company_id' => $request->user()->company_id],
            [
                'name' => $request->name,
                'company_id' => $request->user()->company_id,
                'city' => $request->city,
                'house_number' => $request->house_number,
                'neighborhood' => $request->neighborhood
            ],
        );

        return $this->RespondSuccess('Dados atualizado com sucesso');
    }


    public function destroy(string $id)
    {
    }
}
