<?php

namespace App\Services\HumanResource\Payroll;

use Hashids\Hashids;
use App\Models\User;
use App\Models\UserDeduction;
use App\Models\UserOrganization;
use App\Models\Payroll;
use App\Models\PayrollCycle;
use App\Models\PayrollCutoff;
use App\Models\PayrollDeduction;
use App\Http\Resources\Hr\Payroll\ListResource;
use App\Http\Resources\Hr\Payroll\Contractual\CycleResource;
use App\Http\Resources\Hr\Payroll\Contractual\CutoffResource;

class ContractualClass
{
    public function lists($request){
        $data = ListResource::collection(
            PayrollCutoff::with('cycle','status')
            ->with('user:id,username','user.profile:id,user_id,firstname,middlename,lastname,suffix_id')
            // ->with('payrolls.deductions.deduction')
            // ->with('payrolls.user.profile:id,user_id,firstname,middlename,lastname,suffix_id')
            // ->with('payrolls.user:id,username','payrolls.user.organization:id,user_id,position_id,salary_id,type_id','payrolls.user.organization.position:id,name','payrolls.user.organization.type:id,name','payrolls.user.organization.salary:id,grade,amount')
            ->withSum('payrolls as total', 'netpay')
            ->withSum('payrolls as deduction', 'deduction')
            ->withSum('payrolls as compensation', 'gross')
            ->withCount('payrolls as count')
            ->whereHas('cycle', function ($query) {
                $query->where('is_regular',0);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->count)
        );
        return $data;
    }

    public function view($code){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($code);

        $data = new CutoffResource(
            PayrollCutoff::query()
            ->with('cycle','status')
            ->with('user:id,username','user.profile:id,user_id,firstname,middlename,lastname,suffix_id')
            ->with('payrolls.deductions.deduction')
            ->with('payrolls.user.profile:id,user_id,firstname,middlename,lastname,suffix_id')
            ->with('payrolls.user:id,username','payrolls.user.organization:id,user_id,position_id,salary_id,type_id','payrolls.user.organization.type:id,name','payrolls.user.organization.position:id,name','payrolls.user.organization.salary:id,grade,amount','payrolls.user.deductions.deduction')
            ->withSum('payrolls as total', 'netpay')
            ->withCount('payrolls as count')
            ->where('id',$id)->first()
        );
        return $data;
    }

    public function cycle($request){
        $year = $request->year;
        $month = $request->month;

        $cycle = PayrollCycle::where('month',$month)->where('year',$year)->where('is_regular',0)->first();
        if($cycle){
            $batch = PayrollCutoff::where('type',$request->type)->where('cycle_id',$cycle->id)->count();
            $data = PayrollCutoff::create(
                array_merge($request->all(), [
                    'code' => $this->generateCode2(),
                    'user_id' => \Auth::user()->id,
                    'batch' => $batch + 1,
                    'cycle_id' => $cycle->id,
                    'status_id' => 17
                ])
            );
        }else{
            $data = PayrollCycle::create(array_merge($request->all(), [
                'code' => $this->generateCode(),
                'user_id' => \Auth::user()->id
            ]));
            $cutoff = $data->cutoffs()->create(
                array_merge($request->all(), [
                    'code' => $this->generateCode2(),
                    'user_id' => \Auth::user()->id,
                    'batch' => 1,
                    'status_id' => 17
                ])
            );
        }
        return [
            'data' => new CycleResource($data),
            'message' => 'Cycle creation was successful!', 
            'info' => "You've successfully created a new cycle."
        ];
    }

    private function generateCode()
    {
        return \DB::transaction(function () {
            $year = date('Y');
            $month = date('m');
            $count = PayrollCycle::whereYear('created_at', $year)
                ->whereNotNull('code')
                ->lockForUpdate()
                ->count();
            $next = $count + 1;
            $code = "R9-{$month}{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
            while (PayrollCycle::where('code', $code)->exists()) {
                $next++;
                $code = "R9-{$month}{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
            }
            return $code;
        });
    }

    private function generateCode2()
    {
        return \DB::transaction(function () {
            $year = date('Y');
            $month = date('m');
            $count = PayrollCutoff::whereYear('created_at', $year)->whereNotNull('code')->lockForUpdate()->count();
            $next = $count + 1;
            $code = "R9CFF-{$month}{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
            while (PayrollCutoff::where('code', $code)->exists()) {
                $next++;
                $code = "R9CFF-{$month}{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
            }
            return $code;
        });
    }
}
