<?php

namespace App\Http\Controllers\FAIMS\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FAIMS\Procurement\SupplierClass;
use App\Services\FAIMS\Procurement\ViewClass;
use App\Traits\HandlesTransaction;

class SupplierController extends Controller
{
     use HandlesTransaction;

    public $supplier, $view;

    public function __construct(
        SupplierClass $supplier, 
        ViewClass $view, 
    ){
        $this->supplier = $supplier;
        $this->view = $view;
    }

    public function index(Request $request){
        switch($request->option){     
            case 'lists':
                  return $this->supplier->lists($request);
            break;

            default:
                return inertia('Modules/FAIMS/Procurement/Suppliers/Index', [
                    'dropdowns' => [
                        'roles'  =>  \Auth::user()->roles,
                    ],
                ]); 
        }   
    }

     public function store(Request $request) {
        $result = $this->handleTransaction(function () use ($request) {
            return $this->supplier->save($request);
        });

        return back()->with([
            'data' => $result['data'],
            'message' => $result['message'],
            'info' => $result['info'],
            'status' => $result['status'],
        ]);
        
    }

    
    public function update(Request $request) {
        $result = $this->handleTransaction(function () use ($request) {
            return $this->supplier->save($request);
        });

        return back()->with([
            'data' => $result['data'],
            'message' => $result['message'],
            'info' => $result['info'],
            'status' => $result['status'],
        ]);
        
    }
    


}
