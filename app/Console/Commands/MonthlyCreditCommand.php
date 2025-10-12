<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserCredit;
use App\Models\ListLeave;
use Illuminate\Console\Command;

class MonthlyCreditCommand extends Command
{
    protected $signature = 'credit:monthly';
    protected $description = 'Auto add monthly accrual rate.';

    public function handle()
    {
        $year = now()->year;
        $month = now()->month;

        $users = User::with('profile')->whereHas('organization', function ($query) {
            $query->where('type_id', 15)->where('status_id',2);
        })->get();

        foreach($users as $user){
            $leaves = ListLeave::where('is_regular',1)->where('is_active',1)->where('renewal_period','monthly')->get();
            foreach($leaves as $leave){
                
                $credit = UserCredit::where('leave_id',$leave->id)->where('user_id',$user->id)->where('is_active',1)->where('year',$year)->first();
                if($credit){
                    $alreadyLogged = $credit->logs()->where('remarks','accrual for '. now()->format('F Y'))->whereMonth('created_at', $month)->whereYear('created_at', $year)->exists();
                
                    if ($alreadyLogged) {
                        continue; 
                    }

                    $old_balance = $credit->balance;
                    $credit->balance = $credit->balance + $leave->accrual_rate;
                    $credit->earned = $credit->earned + $leave->accrual_rate;
                    if($credit->save()){
                        $credit->logs()->create([
                            'type_id' => $leave->id,
                            'amount' => $leave->accrual_rate,
                            'old_balance' => $old_balance,
                            'new_balance' => $credit->balance,
                            'remarks' => 'accrual for '. now()->format('F Y')
                        ]);
                    }
                }else{
                    $leaves2 = ListLeave::where('is_active',1)->where('is_requested',0)->get();
                    foreach($leaves2 as $leave){
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
                            $carried = 0 ;
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
    }
}
