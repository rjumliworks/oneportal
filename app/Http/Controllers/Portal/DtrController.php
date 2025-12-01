<?php

namespace App\Http\Controllers\pORTAL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DropdownClass;
use App\Services\Portal\Dtr\ViewClass;
use App\Traits\HandlesTransaction;

class DtrController extends Controller
{
    use HandlesTransaction;

    public $view,$dropdown;

    public function __construct(
        ViewClass $view, 
        DropdownClass $dropdown
    ){
        $this->view = $view;
        $this->dropdown = $dropdown;
    }

    public function index(Request $request){
        switch($request->option){
            case 'dtr':
                return $this->view->dtr($request);
            break;
            default:
                return inertia('Modules/Portal/Dtr/Index'); 
        }   
    }
}
