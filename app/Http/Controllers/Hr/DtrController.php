<?php

namespace App\Http\Controllers\Hr;

use App\Traits\HandlesTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HumanResource\Dtr\SaveClass;
use App\Services\HumanResource\Dtr\ViewClass;
use App\Services\HumanResource\Dtr\PrintClass;
use App\Services\HumanResource\Dtr\UpdateClass;

class DtrController extends Controller
{
    use HandlesTransaction;

    protected $save, $update, $view, $print;

    public function __construct(SaveClass $save, ViewClass $view, UpdateClass $update, PrintClass $print){
        $this->save = $save;
        $this->view = $view;
        $this->print = $print;
        $this->update = $update;
    }

    public function index(Request $request){
        switch($request->option){
            case 'lists':
                return [];
            break;
            case 'print':
                return [];
            break;
            default:
               return inertia('Modules/HumanResource/Dtr/Index');
        }   
    }
}
