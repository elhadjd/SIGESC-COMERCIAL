<?php

namespace App\Http\Controllers\Funcionarios;

use App\classes\ActivityRegister;
use App\classes\uploadImage;
use App\Http\Controllers\Controller;
use App\Models\expense;
use App\Models\session;
use App\Models\Workers;
use App\Models\WorkersDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use PharIo\Manifest\Author;

class funcionarioController extends Controller
{
    public function Index()
    {
        $register = new ActivityRegister;
        $register->Activity("Entrou no module funcionario");
        return Inertia::render('Funcionarios/index',[
            'user'=>Auth::user()
        ]);
    }

    public function get(Request $request,WorkersDepartment $workersDepartment)
    {
        return [
            'workers' => $request->user()->company()->first()->workers()->get(),
            'departments' => $this->departments($workersDepartment),
            'storeWorkers' => $request->user()->company()->first()->workers()->get()
        ];
    }

    public function departments()
    {
        return $this->companyUser()->DepartmentWorkers()->get();
    }

    public function newWorker()
    {
        $register = new ActivityRegister;
        $register->Activity("Criou um funcionario");
        $create = Workers::create([
            "company_id"=> $this->companyUser()->id
        ]);

        return $this->show($create->id);
    }

    public function show($id)
    {
        return Workers::find($id);
    }

    public function saveDepartment(Request $request)
    {
        if ($request->id == null) {
            $create = WorkersDepartment::create([
                'company_id'=>$this->companyUser()->id,
                'name'=> $request->name
            ]);
        } else{
            $create = WorkersDepartment::find($request->id)->update([
                'name'=> $request->name
            ]);
        }
        return $create;
    }

    public function saverWorker(Request $request , Workers $worker)
    {
        $data = $request->all();
        $image = new uploadImage();
        if($request->imagem) {
            $data['image'] = $image->Upload('/worker/image/',$request->imagem);
        }else {
            $data['image'] = $request->image;
        }

        $update = [
            'image' => $data['image'],
            'name' => $request->name,
            'cargo' => $request->cargo,
            'email' => $request->email,
            'company_id' => $request->user()->company_id,
            'phone' => $request->phone,
            'user_id' => $request->user['id'],
            'celular' => $request->celular,
            'workers_department_id' => $request->workers_department_id['id'],
            'manager' => $request->manager['id'],
            'trainer' => $request->trainer['id'],
            'hourWork' => $request->hourWork,
            'salary' => $request->salary,
            'dailyExpense' => $request->dailyExpense
        ];

        if ($worker->update($update)) return $this->RespondSuccess('Dados atualizado com sucesso');

        return $this->RespondError('Erro ao atualizar os dados , por favor tenta novamente');
    }

    public function AnalisWorker(Request $request,$month)
    {
       $session_worker = session::whereMonth('created_at',date('m'))->get();

       $workers = $request->user()->company()->first()
       ->workers()->with(['expenses' => function($query) use ($month){
           $query->orderBy('id','ASC');
           $query->whereMonth('created_at',$month);
       }])->get();

       return [
         'sessions' => $session_worker,
         'workers' => $workers
       ];
    }

    public function saveExpense(Request $request)
    {
        $insert = [
            'worker_id'=> $request->worker['id'],
            'type'=> $request->type,
            'value'=> $request->value,
            'numberInvoice'=> $request->numberInvoice,
        ];
        if (!expense::create($insert)) return $this->RespondError('Aconteceu um erro ao inserir');
        return $this->RespondSuccess('Operação feita com sucesso');
    }

}
