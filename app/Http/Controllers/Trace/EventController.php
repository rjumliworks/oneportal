<?php

namespace App\Http\Controllers\Trace;

use App\Traits\HandlesTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DropdownClass;

class EventController extends Controller
{
    use HandlesTransaction;

    public $dropdown;


    public function __construct(DropdownClass $dropdown){
        $this->dropdown = $dropdown;
    }

    public function index(Request $request){
        switch($request->option){
            case 'list':
                return [];
            break;
            default:
                return inertia('Modules/Trace/Events/Index',[
                    'dropdowns' => [
                        'types' => $this->dropdown->datas('Event'),
                        'modes' => $this->dropdown->datas('Event Mode'),
                        'audiences' => $this->dropdown->datas('Audience')
                    ]
                ]); 
        }   
    }
}
