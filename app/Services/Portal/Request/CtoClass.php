<?php

namespace App\Services\Portal\Request;

use App\Models\Request;

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
}
