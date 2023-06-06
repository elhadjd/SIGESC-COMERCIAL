<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\movement_type_produtos;
use App\Models\produtos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class movementsController extends Controller
{
    public function get(produtos $product , movement_type_produtos $movements,$movement_type)
    {
        $movements = movement_type_produtos::with('product')
        ->with('armagen')
        ->with('user')
        ->with('movement_type')
        ->where('produtos_id',$product->id)
        ->where('movement_type_id',$movement_type)
        ->select($movements::raw('DATE(created_at) as dia'),'quantity', 'id' , 'quantityAfter' ,'produtos_id','motive','order_pos_id','movement_type_id','quantity')
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('dia');
        return $movements;
    }
}
