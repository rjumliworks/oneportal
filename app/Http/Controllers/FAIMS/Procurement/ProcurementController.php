<?php

namespace App\Http\Controllers\FAIMS\Procurement;

use App\Http\Controllers\Controller;
use App\Traits\HandlesTransaction;
use Illuminate\Http\Request;
use App\Services\DropdownClass;
use App\Services\FAIMS\Procurement\ViewClass;

class ProcurementController extends Controller
{
     use HandlesTransaction;

    public $dropdown, $view;

    public function __construct(
        DropdownClass $dropdown,
        ViewClass $view, 
    ){
        $this->dropdown = $dropdown;
        $this->view = $view;
    }

    public function index(Request $request){
        switch($request->option){     
            case 'lists':
                return $this->view->procurements($request);
            break;
            default:
                return inertia('Modules/FAIMS/Procurement/Index', [
                    'dropdowns' => [
                    
                    ],
                ]); 
        }   
    }

    public function create_index(Request $request){
    
        switch($request->option){     
            case 'units':
               return  $this->dropdown->units($request->code);
            break;
            default:
                return inertia('Modules/FAIMS/Procurement/CreatePage', [
                    'dropdowns' => [
                        'divisions' => $this->dropdown->dropdowns('Division'),
                        'fund_clusters' => $this->dropdown->dropdowns('Fund Cluster'),
                        'procurement_codes' => $this->dropdown->procurement_codes(),
                    ],
                ]); 
            break;
        } 
    }


}
