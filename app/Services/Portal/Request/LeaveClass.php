<?php

namespace App\Services\Portal\Request;

use Carbon\Carbon;
use App\Models\Request;
use App\Models\RequestLeave;
use App\Models\RequestSignatory;
use App\Models\RequestReport;
use App\Models\UserCredit;

class LeaveClass
{
    public function store($request){
        $division_id = \Auth::user()->organization->division_id;
        $data = Request::create([
            'code' => $this->generateRequestCode(),
            'type_id' => 158,
            'user_id' => \Auth::user()->id
        ]);
        if($data){
            $signatory = $data->signatories()->create([
                'division_id' => $division_id,
                'code' => $this->generateCode($data->type_id),
                'status_id' => ($division_id == 2) ? 25 : 24,
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
            // $this->report($data->id);
        }

        return [
            'data' => $data,
            'message' => 'Leave Request Submitted', 
            'info' => "Your leave request has been submitted. Keep an eye on your notifications for any approvals or updates."
        ];
    }
    
    private function generateCode($type)
    {
        return \DB::transaction(function () use ($type) {
            $latest = RequestSignatory::lockForUpdate()
                ->whereHas('request', function ($query) use ($type){
                    $query->where('type_id',$type);
                })
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->orderByDesc('id')
                ->first();

            $count = $latest
                ? (int) substr($latest->code, -4) + 1
                : 1;

            $code = now()->format('Y') .'-'.str_pad($count, 4, '0', STR_PAD_LEFT);

            return $code;
        });
    }


    private function generateRequestCode()
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

    private function report($id){
        $data = RequestLeave::with([
            'detail',
            'type',
            'credits.log','credits.credit.leave',
            'request.comments.user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'request.comments.replies.user.profile:user_id,firstname,middlename,lastname,avatar,avatar,suffix_id',
            'request.tags.user:id',
            'request.tags.user.profile:user_id,firstname,middlename,lastname,avatar,signature,suffix_id',
            'request.statuses.user:id',
            'request.statuses.user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'request.statuses.status',
            'request.status',
            'request.type',
            'request.dates',
            'request.detail',
            'request.user:id',
            'request.user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'request.user.organization.position','request.user.organization.salary','request.user.organization.division','request.user.organization.unit',
            'request.signatories.division','request.signatories.approved','request.signatories.recommended'
        ])
        ->where('request_id',$id)
        ->first();

        $user = $data->request->tags[0]->user;
  
        $employee = [
            'lastname' => $user->profile->lastname,
            'middlename' => $user->profile->middlename,
            'firstname' => $user->profile->firstname,
            'signature' => $user->profile->signature,
            'position' => $user->organization->position->name ?? 'n/a',
            'salary' => $user->organization->salary->amount ?? 0,
            'position_short' => $user->organization->position->short ?? 'n/a',
            'unit' => $user->organization->unit->name ?? 'n/a',
            'unit_short' => $user->organization->unit->short ?? 'n/a',
            'division' => $user->organization->division->name ?? 'n/a',
            'division_id' => $user->organization->division->id ?? null
        ];

        $divisions[] = $user->organization->division->name;
 

        if (count($data->request->dates) === 1) {
            // Only one date range
            $start = Carbon::parse($data->request->dates[0]->start);
            $end = Carbon::parse($data->request->dates[0]->end);

            if ($start->equalTo($end)) {
                $formattedDateRange = $start->format('F j, Y');
            } elseif ($start->format('F Y') === $end->format('F Y')) {
                $formattedDateRange = $start->format('F j') . '–' . $end->format('j, Y');
            } else {
                $formattedDateRange = $start->format('F j, Y') . ' – ' . $end->format('F j, Y');
            }
        } else {
            // Multiple date ranges
            $ranges = [];

            foreach ($data->request->dates as $date) {
                $start = Carbon::parse($date->start);
                $end = Carbon::parse($date->end);

                if ($start->equalTo($end)) {
                    $ranges[] = $start->format('M j, Y');
                } elseif ($start->format('F Y') === $end->format('M Y')) {
                    $ranges[] = $start->format('M j') . '–' . $end->format('j, Y');
                } else {
                    $ranges[] = $start->format('M j, Y') . ' – ' . $end->format('M j, Y');
                }
            }

            // Join multiple ranges with comma
            $formattedDateRange = implode(', ', $ranges);
        }
        if($data->request->signatories[0]->approved){
            $approved = [
                'name' => $data->signatories[0]->approved->user->profile->fullname,
                'signature' => $data->signatories[0]->approved->user->profile->signature,
                'role' => ($data->signatories[0]->approved->is_designated) ? 'Regional Director' : 'OIC - Regional Director'
            ];
        }else{
            $approved = null;
        }

        if($data->request->signatories[0]->recommended){
            $recommended = [
                'name' => $data->signatories[0]->recommended->user->profile->fullname,
                'signature' => $data->signatories[0]->recommended->user->profile->signature,
                'role' => ($data->signatories[0]->recommended->is_designated) ? 'Assistant Regional Director' : 'OIC - Assistant Regional Director',
                'division' => $data->signatories[0]->division->others
            ];
        }else{
            $recommended = null;
        }

        $information = [
            'code' => $data->request->code,
            'count' => $data->count,
            'detail' => $data->detail,
            'type' => $data->type,
            'credits' => $data->credits,
            'purpose' => $data->request->detail->purpose,
            'date' => $formattedDateRange,
            'employee' => $employee,
            'approved' => $approved,
            'recommended' => $recommended,
            'divisions' => $divisions,
            'created_by' => $data->request->user->profile->fullname,
            'created_at' => Carbon::parse($data->request->created_at)->format('d F Y')
        ];

        if(RequestReport::where('request_id',$id)->count() > 0){
            $data = RequestReport::where('request_id',$id)->first();
            $data->information = json_encode($information);
            $data->save();
        }else{
            $data = RequestReport::create([
                'information' => json_encode($information,true),
                'request_id' => $id
            ]);
        }
        return true;
    }
}
