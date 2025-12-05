<?php

namespace App\Http\Controllers\FAIMS\Procurement;

use App\Http\Controllers\Controller;
use App\Traits\HandlesTransaction;
use Illuminate\Http\Request;
use App\Services\DropdownClass;
use App\Services\FAIMS\Procurement\ViewClass;
use App\Services\FAIMS\Procurement\ProcurementClass;
use App\Services\FAIMS\Procurement\PrintClass;
use App\Services\System\User\UserClass;


class ProcurementController extends Controller
{
     use HandlesTransaction;

    public $dropdown, $view, $procurement , $user , $print;

    public function __construct(
        DropdownClass $dropdown,
        ViewClass $view, 
        PrintClass $print, 
        ProcurementClass $procurement,
        UserClass $user,
    ){
        $this->dropdown = $dropdown;
        $this->view = $view;
        $this->print = $print;
        $this->procurement = $procurement;
        $this->user = $user;
    }

    public function index(Request $request){
        switch($request->option){     
            case 'lists':
                return $this->view->procurements($request);
            break;

            case 'quotations':
                return $this->view->quotations($request);
            break;

            default:
                return inertia('Modules/FAIMS/Procurement/Index', [
                    'dropdowns' => [
                        'roles'  =>  \Auth::user()->roles,
                        'designation'  =>  \Auth::user()->designation,
                       
                    ],
                     'regional_director'  =>  $this->dropdown->regional_director(),
                   
                ]); 
        }   
    }

    public function create_index(Request $request){
        
        switch($request->option){     
            case 'units':
               return  $this->dropdown->units($request->code);
            break;
            case 'unit_type':
                return $this->dropdown->unit_type($request->code);
            break;
            case 'title':
                return $this->procurement->procurement_title($request->id);
            break;
            default:
                return inertia('Modules/FAIMS/Procurement/CreatePage', [
                    'dropdowns' => [
                        'divisions' => $this->dropdown->dropdowns('Division'),
                        'fund_clusters' => $this->dropdown->dropdowns('Fund Cluster'),
                        'procurement_codes' => $this->dropdown->procurement_codes(),
                        'unit_types' => $this->dropdown->unit_types(),
                        'requesters' => $this->dropdown->requesters(),
                        'approvers' => $this->dropdown->approvers(),     
                    ],
                    'option' => $request->option,
                ]); 
            break;
        } 
    }

    public function store(Request $request) {
        $result = $this->handleTransaction(function () use ($request) {
            return $this->procurement->save($request);
        });

        return redirect()->route('procurement.index')->with([
            'data' => $result['data'],
            'message' => $result['message'],
            'info' => $result['info'],
            'status' => $result['status'],
        ]);

    }



     public function update($id, Request $request) {

        $result = $this->handleTransaction(function () use ($id, $request) {
            switch($request->option){     
                case 'edit':
                    return $this->procurement->update($id, $request);
                break;
                case 'review':
                    return $this->procurement->review($id, $request);
                break;
                case 'approve':
                    return $this->procurement->approve($id, $request);
                break;
            }   
           
        });

        return redirect()->route('procurement.index')->with([
            'data' => $result['data'],
            'message' => $result['message'],
            'info' => $result['info'],
            'status' => $result['status'],
        ]);

    }

    public function show($id, Request $request){
        if($request->type){
            return $this->print->print($id, $request);
        }
        else{
            return $this->view->show($id, $request);
        }
        
    }


}
