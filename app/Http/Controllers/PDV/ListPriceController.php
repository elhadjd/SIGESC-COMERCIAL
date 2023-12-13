<?php

namespace App\Http\Controllers\PDV;

use App\Http\Controllers\Controller;
use App\Models\price_list;
use App\Models\produtos;
use Illuminate\Http\Request;

class ListPriceController extends Controller
{

    public function index()
    {

    }

    public function create(Request $request)
    {

        $request->validate([
            'quantity' => 'required|integer',
            'price_discount' => 'required|integer',
            'company_id' => 'required|integer',
            'produtos_id' => 'required|integer',
        ]);

        price_list::create($request->all());

        $lista_price = produtos::find($request->produtos_id);

        return $this->RespondSuccess(
            __('Operation completed successfully'),
            $lista_price
        );
    }

    public function update(Request $request, price_list $listPrice,produtos $product)
    {
        $listPrice->update([
            'quantity' => $request->quantity,
            'price_discount' => $request->price_discount
        ]);
        return $product->refresh();
    }

    public function destroy($id, produtos $product)
    {
       price_list::find($id)->delete();

        return $this->RespondSuccess(
            __('Item deleted successfully'),
            $product->refresh()
        );
    }
}
