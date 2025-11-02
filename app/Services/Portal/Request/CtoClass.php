<?php

namespace App\Services\Portal\Request;

use Carbon\Carbon;
use App\Models\Request;
use App\Models\RequestReport;

class CtoClass
{
    public function store($request){
        $division_id = \Auth::user()->organization->division_id;
        $data = Request::create([
            'code' => $this->generateCode(),
            'type_id' => 165,
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
                'purpose' => ($request->purpose) ?  $request->purpose : 'n/a',
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
            }

            $this->report($data->id);
        }

        return [
            'data' => $data,
            'message' => 'Render Overtime Service Request Submitted', 
            'info' => "Your overtime request has been submitted. Keep an eye on your notifications for any approvals or updates."
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

            $code = 'REQUEST-' . now()->format('mY') . '-CTO-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            return $code;
        });
    }

    private function report($id){
        $data = Request::with([
            'tags.user:id',
            'tags.user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'tags.user.organization.division','tags.user.organization.position','tags.user.organization.unit',
            'type',
            'dates',
            'detail',
            'user:id',
            'user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'signatories.division',
            'signatories.approved.user.profile','signatories.approved.signatory.designationable.designation',
            'signatories.recommended.user.profile','signatories.recommended.signatory.designationable.designation'
        ])
        ->where('id',$id)
        ->first();

        $users = $data->tags;
        foreach ($users as $tag) {
            $user = $tag->user;
            $division = $user->organization->division->name ?? 'n/a';
            $division_id = $user->organization->division->id ?? null;

            $employees[] = [
                'name' => $user->profile->name,
                'position' => $user->organization->position->name ?? 'n/a',
                'position_short' => $user->organization->position->short ?? 'n/a',
                'unit' => $user->organization->unit->name ?? 'n/a',
                'unit_short' => $user->organization->unit->short ?? 'n/a',
                'division' => $division,
                'division_id' => $division_id,
            ];

            $divisions[] = $division;
        }

        $start = Carbon::parse($data->dates[0]->start);
        $end = Carbon::parse($data->dates[0]->end);
        if($start->format('Y-m-d') === $end->format('Y-m-d')) {
            $formattedDateRange = $start->format('F j, Y');
        }else if($start->format('F Y') === $end->format('F Y')) {
            $formattedDateRange = $start->format('F j') . '–' . $end->format('j, Y');
        }else{
            $formattedDateRange = $start->format('F j, Y') . ' – ' . $end->format('F j, Y');
        }

        if($data->signatories[0]->approved){
            $approved = [
                'name' => $data->signatories[0]->approved->user->profile->fullname,
                'signature' => $data->signatories[0]->approved->user->profile->signature,
                'role' => ($data->signatories[0]->approved->is_designated) ? 'Regional Director' : 'OIC - Regional Director'
            ];
        }else{
            $approved = null;
        }

        if($data->signatories[0]->recommended){
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
            'code' => $data->code,
            'purpose' => $data->detail->purpose,
            'date' => $formattedDateRange,
            'employees' => $employees,
            'divisions' => $divisions,
            'approved' => $approved,
            'recommended' => $recommended,
            'created_by' => $data->user->profile->fullname,
            'created_at' => $data->created_at
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
