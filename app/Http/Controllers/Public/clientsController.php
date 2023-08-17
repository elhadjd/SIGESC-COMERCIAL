<?php

namespace App\Http\Controllers\Public;

use App\classes\uploadImage;
use App\Http\Controllers\Controller;
use App\Models\cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class clientsController extends Controller
{
    public function get()
    {
        return cliente::where('company_id',Auth::user()->company_id)->get();
    }

    public function getClientsCredit(cliente $clients)
    {
        return $clients->with('invoices')->withSum('invoices','TotalInvoice')->withSum('invoices','RestPayable')->get();
    }

    public function clientOrders(cliente $client)
    {
        return $client->invoices()->count();
    }

    public function updateClient(cliente $client , Request $request)
    {
        $Image = new uploadImage();
        $request->validate([
            'name'=> 'required||string'
        ]);

        $data = $request->all();

        unset($data['id'],$data['created_at'],$data['updated_at']);

        if($data['images']!= null) $data['image'] = $Image->Upload('/clientes/image/',$request->images);

        if ($client->update($data)) return $this->RespondSuccess('Sucesso',$client->fresh());

        return $this->RespondError('Erro ao salvar fornecedore');
    }

    public function delete(cliente $client)
    {
        $order = $client->load('invoices');

        if ($order->invoices->count() >0 ) return $this->RespondError('Não é posivel apagar este Cliente');
        if ($order->delete()) return $this->RespondSuccess('Cliente eliminado com sucesso');
    }

    public function show($id)
    {
        return cliente::find($id);
    }

    public function createClient()
    {
        $create = cliente::create([
            'company_id'=>Auth::user()->company_id,
            'state'=>1
        ]);
        return $this->show($create->id);
    }
}
