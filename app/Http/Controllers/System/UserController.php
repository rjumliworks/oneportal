<?php

namespace App\Http\Controllers\System;

use App\Services\DropdownClass;
use App\Traits\HandlesTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\System\User\UserClass;
use App\Http\Requests\System\UserRequest;

class UserController extends Controller
{
    use HandlesTransaction;

    public $user,$dropdown;

    public function __construct(UserClass $user, DropdownClass $dropdown){
        $this->dropdown = $dropdown;
        $this->user = $user;
    }

    public function index(Request $request){
        switch($request->option){
            case 'list':
                return $this->user->list($request);
            break;
            default:
                return inertia('Modules/System/Users/Index',[
                    'dropdowns' => [
                        'roles' => $this->dropdown->roles()
                    ]
                ]); 
        }   
    }

     public function update(UserRequest $request){
        $result = $this->handleTransaction(function () use ($request) {
            switch($request->option){
                case 'status':
                    return $this->user->status($request);
                break;
                case 'credential':
                    return $this->user->credential($request);
                break;
                case 'role':
                    return $this->user->role($request);
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
