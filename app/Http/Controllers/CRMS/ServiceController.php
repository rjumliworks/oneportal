<?php

namespace App\Http\Controllers\CRMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FAIMS\Procurement\ProcurementBACClass;
use App\Services\FAIMS\Procurement\ViewClass;
use App\Services\FAIMS\Procurement\PrintClass;
use App\Services\DropdownClass;
use App\Traits\HandlesTransaction;

class ServiceController extends Controller
{
     use HandlesTransaction;

     public $dropdown, $view, $service , $user , $print;

    public function __construct(
        ProcurementBACClass $service, 
        ViewClass $view,
        PrintClass $print, 
        DropdownClass $dropdown
    ){
        $this->service = $service;
        $this->print = $print;
        $this->dropdown = $dropdown;
        $this->view = $view;
    }

    public function index(Request $request){
        switch($request->option){     
            case 'lists':
                  return $this->service->lists($request);
            break;
            default:
                return inertia('Modules/CRMS/Services/Index', [
                    'dropdowns' => [
                        'roles'  =>  \Auth::user()->roles,
                        'divisions' => $this->dropdown->dropdowns('Division'),
                        'units' => $this->dropdown->list_units(),
                    ],
                   
                ]); 
            break;

        }   
    }



}
