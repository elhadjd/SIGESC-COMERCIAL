<?php

namespace App\Http\Controllers\Public;

use App\classes\CheckData;
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
        return $clients->where('company_id',$this->companyUser()->id)->with('invoices')->withSum('invoices','TotalInvoice')->withSum('invoices','RestPayable')->get();
    }

    public function clientOrders(cliente $client)
    {
        return $client->invoices()->count();
    }

    public function updateClient(cliente $client , Request $request)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Clients','Edit')) return $this->RespondError(__('User without access'));
        $Image = new uploadImage();
        $request->validate([
            'name'=> 'required||string'
        ]);

        $data = $request->all();

        unset($data['id'],$data['created_at'],$data['updated_at']);

        if($data['images']!= null) $data['image'] = $Image->Upload('/clientes/image/',$request->images);

        if ($client->update($data)) return $this->RespondSuccess(__('Operation completed successfully'),$client->fresh());

        return $this->RespondError(__('A server error occurred'));
    }

    public function delete(cliente $client)
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Clients','Delete')) return $this->RespondError(__('User without access'));
        $order = $client->load('invoices');

        if ($order->invoices->count() >0 ) return $this->RespondError(__('It is not possible to delete this client'));
        if ($order->delete()) return $this->RespondSuccess(__('Item deleted successfully'));
    }

    public function show($id)
    {
        return cliente::find($id);
    }

    public function createClient()
    {
        $checkPermission = new CheckData;
        if(!$checkPermission->checkPermission('Clients','Create')) return $this->RespondError(__('User without access'));
        $create = cliente::create([
            'company_id'=>Auth::user()->company_id,
            'state'=>1
        ]);
        return $this->show($create->id);
    }
}
