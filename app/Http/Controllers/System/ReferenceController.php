<?php

namespace App\Http\Controllers\System;

use App\Traits\HandlesTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DropdownClass;
use App\Services\System\Reference\UnitClass;
use App\Services\System\Reference\StatusClass;
use App\Services\System\Reference\LeaveClass;
use App\Services\System\Reference\SalaryClass;
use App\Services\System\Reference\PositionClass;
use App\Services\System\Reference\DeductionClass;

class ReferenceController extends Controller
{
    use HandlesTransaction;

    public $unit, $status, $leave, $salary, $position, $deduction, $dropdown;

    public function __construct(
        UnitClass $unit, 
        LeaveClass $leave, 
        StatusClass $status, 
        SalaryClass $salary, 
        PositionClass $position, 
        DeductionClass $deduction, 
        DropdownClass $dropdown
        ){
        $this->unit = $unit;
        $this->leave = $leave;
        $this->status = $status;
        $this->salary = $salary;
        $this->position = $position;
        $this->deduction = $deduction;
        $this->dropdown = $dropdown;
    }

    public function show($code){
        switch($code){
            case 'positions':
                return inertia('Modules/System/References/Positions/Index',[
                    'lists' => $this->position->lists()
                ]);
            break;
            case 'deductions':
                return inertia('Modules/System/References/Deductions/Index',[
                    'lists' => $this->deduction->lists()
                ]);
            break;
            case 'salaries':
                return inertia('Modules/System/References/Salaries/Index',[
                    'lists' => $this->salary->lists()
                ]);
            break;
            case 'statuses':
                return inertia('Modules/System/References/Statuses/Index',[
                    'lists' => $this->status->lists()
                ]);
            break;
            case 'units':
                return inertia('Modules/System/References/Units/Index',[
                    'lists' => $this->unit->lists(),
                    'divisions' => $this->dropdown->dropdowns('Division'),
                ]);
            break;
            case 'leaves':
                return inertia('Modules/System/References/Leaves/Index',[
                    'lists' => $this->leave->lists()
                ]);
            break;
        }
    }

    public function update(Request $request){
        $result = $this->handleTransaction(function () use ($request) {
            switch($request->option){
                case 'unit':
                    return $this->unit->update($request);
                break;
                case 'position':
                    return $this->position->update($request);
                break;
                case 'salary':
                    return $this->salary->update($request);
                break;
                case 'leave':
                    return $this->leave->update($request);
                break;
                case 'deduction':
                    return $this->deduction->update($request);
                break;
                case 'status':
                    return $this->status->update($request);
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
