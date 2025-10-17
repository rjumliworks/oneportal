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
                return $this->view->list($request);
            break;
            case 'print':
                return $this->print->dtr($request);
            break;
            default:
               return inertia('Modules/HumanResource/Dtr/Index');
        }   
    }

    public function update(Request $request){
        $result = $this->handleTransaction(function () use ($request) {
            switch($request->option){
                case 'dtr':
                    return $this->update->save($request);
                break;
                case 'add':
                    return $this->update->add($request);
                break;
            }
        });

        return back()->with([
            'data' => $result['data'],
            'message' => $result['message'],
            'info' => $result['info'],
            'status' => $result['status'],
        ]);
    }
}
