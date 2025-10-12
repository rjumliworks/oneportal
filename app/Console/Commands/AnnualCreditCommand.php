<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserCredit;
use App\Models\listLeave;
use Illuminate\Console\Command;

class AnnualCreditCommand extends Command
{
     protected $signature = 'credit:annual';
    protected $description = 'Auto add yearly accrual rate.';

    public function handle()
    {
        // $year = now()->year;
        $year = 2026;

        $users = User::with('profile')->whereHas('organization', function ($query) {
            $query->where('type_id', 15)->where('status_id',2);
        })->get();

        $credit = UserCredit::where('is_active',1)->where('year',$year-1)->update(['is_active' => 0]);

        foreach($users as $user){
            $leaves = ListLeave::where('is_active',1)->where('is_requested',0)->get();
            foreach($leaves as $leave){
                if($leave->renewal_period == 'yearly'){
                    if($leave->id == 7){
                        if($user->profile->is_soloparent){
                            $credit = $user->credits()->create([
                                'leave_id' => $leave->id,
                                'user_id' => $user->id,
                                'balance' => $leave->max_days,
                                'earned' => $leave->max_days,
                                'used' => 0,
                                'year' => $year
                            ]);
                        }
                    }else{
                        $credit = $user->credits()->create([
                            'leave_id' => $leave->id,
                            'user_id' => $user->id,
                            'balance' => $leave->max_days,
                            'earned' => $leave->max_days,
                            'used' => 0,
                            'year' => $year
                        ]);
                    }
                    $credit->logs()->create([
                        'amount' => $leave->max_days,
                        'old_balance' => 0,
                        'new_balance' => $leave->max_days,
                        'remarks' => null,
                        'user_id' => 1,
                        'is_automated' => 1,
                        'type_id' => 162
                    ]);
                }else if($leave['renewal_period'] == 'monthly'){
                    $usercredit = UserCredit::where('user_id',$user->id)->where('leave_id',$leave->id)->where('is_active',0)->where('year',$year-1)->first();
                    $carried = ($usercredit) ? $usercredit->balance : 0 ;
                    $earned = $leave->accrual_rate;

                    $credit = $user->credits()->create([
                        'leave_id' => $leave->id,
                        'user_id' => $user->id,
                        'balance' => $carried + $earned,
                        'earned' => $earned,
                        'carried_over' => $carried,
                        'used' => 0,
                        'year' => $year
                    ]);
                    
                    $credit->logs()->create([
                        'amount' => $earned,
                        'old_balance' => $carried,
                        'new_balance' => $carried + $earned,
                        'remarks' => 'Accrual for ' .now()->format('F Y'),
                        'user_id' => 1,
                        'is_automated' => 1,
                        'type_id' => 162
                    ]);
                }
                
            }
        }
    }
}
