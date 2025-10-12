<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DropdownClass;
use App\Services\Dashboard\EmployeeClass;

class DashboardController extends Controller
{
    protected $dropdown;

    public function __construct(
            DropdownClass $dropdown
        ){
        $this->dropdown = $dropdown;
    }

    public function index(Request $request){
        return inertia('Modules/Portal/Dashboard/Employee',[
            // 'employee' => $this->employee->dashboard()
        ]);
    }

    public function search(Request $request){
        $option = $request->option;
        switch($option){
            case 'provinces':
                return $this->dropdown->provinces($request->code);
            break;
            case 'municipalities':
                return $this->dropdown->municipalities($request->code);
            break;
            case 'barangays':
                return $this->dropdown->barangays($request->code);
            break;
            case 'units':
                return $this->dropdown->units($request->code);
            break;
            case 'users':
                return $this->dropdown->users($request->keyword,$request->is_regular);
            break;
            case 'vehicles':
                return $this->dropdown->vehicles($request->keyword);
            break;
        }
    }
}
