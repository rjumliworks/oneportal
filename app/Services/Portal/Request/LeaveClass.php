<?php

namespace App\Services\Portal\Request;

use App\Models\Request;
use App\Models\UserCredit;

class LeaveClass
{
    public function store($request){
        $division_id = \Auth::user()->organization->division_id;
        $data = Request::create([
            'code' => $this->generateCode(),
            'type_id' => 158,
            'status_id' => ($division_id == 2) ? 25 : 24,
            'user_id' => \Auth::user()->id
        ]);
        if($data){
            $signatory = $data->signatories()->create([
                'division_id' => $division_id,
                'is_approval_only' => ($division_id == 2) ? 1 : 0
            ]);

            $data->tags()->create([
                'user_id' => \Auth::user()->id,
                'division_id' => $division_id,
                'signatory_id' => $signatory->id,
            ]);

            $data->detail()->create([
                'purpose' => ($request->details) ?  $request->details : 'n/a',
            ]);

            if($request->date_type != 'Multiple Dates (non-continuous)'){
                $dates = $request->dates;
                $allWholeDay = array_reduce($dates, function ($carry, $item) {
                    return $carry && ($item['timeOfDay'] === 'Whole Day');
                }, true);

                if ($allWholeDay) {
                    $dates = array_column($dates, 'date');
                    $start = min($dates);
                    $end = max($dates);

                    $data->dates()->create([
                        'start' => $start,
                        'end' => $end,
                        'time' => '08:00',
                    ]);
                } else {
                    foreach($dates as $date){
                        $data->dates()->create([
                            'start' => $date['date'],
                            'end' => $date['date'],
                            'time' => '08:00',
                            'time_of_day' => $date['timeOfDay']
                        ]);
                    }
                    
                }
            }else{
                $dates = $request->dates;
                foreach($dates as $date){
                    $data->dates()->create([
                        'start' => $date['date'],
                        'end' => $date['date'],
                        'time' => '08:00',
                        'time_of_day' => $date['timeOfDay']
                    ]);
                }
            }

            $leave = $data->leave()->create([
                'count' => $request->need_credits,
                'detail_id' => $request->detail_id,
                'type_id' => $request->type_id
            ]);
            if($leave){
                if(\Auth::user()->organization->type_id == 15){
                    $types = $request->types;
                    foreach($types as $type){
                        if($type['required_document']){
                            $credit = new UserCredit;
                            $credit->balance = $type['max_days'] - $request->need_credits;;
                            $credit->used = $request->need_credits;
                            $credit->earned = $type['max_days'];
                            $credit->year = date('Y');
                            $credit->user_id = \Auth::user()->id;
                            $credit->leave_id = $type['value'];
                            $credit->save();
                            if($credit){
                                $log = $credit->logs()->create([
                                    'amount' => $request->need_credits,
                                    'old_balance' => $type['max_days'],
                                    'new_balance' => $credit->balance,
                                    'remarks' => 'Deduction of leave credits for filed leave',
                                    'is_automated' => 1,
                                    'user_id' => 1,
                                    'type_id' => 163
                                ]);
                                if($log){
                                    $leave->credits()->create([
                                        'is_borrowed' => 0,
                                        'log_id' => $log->id,
                                        'credit_id' => $credit->id
                                    ]);
                                }
                            }
                        }else{
                            $credit = UserCredit::where('id',$type['value'])->first();
                            $old_balance = $credit->balance;
                            $credit->balance -= $type['borrow'];
                            $credit->used += $type['borrow'];
                            $credit->save();
                            if($credit){
                                $log = $credit->logs()->create([
                                    'amount' => $type['borrow'],
                                    'old_balance' => $old_balance,
                                    'new_balance' => $credit->balance,
                                    'remarks' => 'Deduction of leave credits for filed leave',
                                    'is_automated' => 1,
                                    'user_id' => 1,
                                    'type_id' => 163
                                ]);
                                if($log){
                                    $leave->credits()->create([
                                        'is_borrowed' => 0,
                                        'log_id' => $log->id,
                                        'credit_id' => $type['value']
                                    ]);
                                }
                            }
                        }
                    }

                    $borrowers = $request->borrowers;
                    if(count($borrowers) > 0){
                        foreach($borrowers as $borrower){
                            $credit = UserCredit::where('id',$borrower['value'])->first();
                            $old_balance = $credit->balance;
                            $credit->balance -= $borrower['borrow'];
                            $credit->used += $borrower['borrow'];
                            $credit->save();
                            if($credit){
                                $log = $credit->logs()->create([
                                    'amount' => $borrower['borrow'],
                                    'old_balance' => $old_balance,
                                    'new_balance' => $credit->balance,
                                    'remarks' => 'Leave credits borrowed and deducted for filed leave',
                                    'is_automated' => 1,
                                    'user_id' => 1,
                                    'type_id' => 163
                                ]);
                                if($log){
                                    $leave->credits()->create([
                                        'is_borrowed' => 1,
                                        'log_id' => $log->id,
                                        'credit_id' => $borrower['value']
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return [
            'data' => $data,
            'message' => 'Leave Request Submitted', 
            'info' => "Your leave request has been submitted. Keep an eye on your notifications for any approvals or updates."
        ];
    }

    private function generateCode()
    {
        return \DB::transaction(function () {
            $latest = Request::lockForUpdate()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->orderByDesc('id')
                ->first();

            $count = $latest
                ? (int) substr($latest->code, -4) + 1
                : 1;

            $code = 'REQUEST-' . now()->format('mY') . '-LEAVE-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            return $code;
        });
    }
}
