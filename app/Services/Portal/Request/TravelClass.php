<?php

namespace App\Services\Portal\Request;

use App\Models\Request;
use App\Models\RequestTravel;
use App\Models\RequestSignatory;

class TravelClass
{
    public function store($request){
        $data = Request::create([
            'code' => $this->generateCode(),
            'type_id' => 156,
            'user_id' => \Auth::user()->id
        ]);
        if($data){
            $divisions = [];
            $signatories = [];

            foreach ($request->tags ?? [] as $user) {
                $divisionId = intval($user['division_id']);

                if(!in_array($divisionId, $divisions)) {
                    $divisions[] = $divisionId;
                }

                if(!empty($user['signatory'])) {
                    $signatory = $data->signatories()->create([
                        'division_id' => $divisionId,
                        'code' => $this->generateTravelSoloCode($divisionId,$data->type_id),
                        'status_id' => 25,
                        'is_approval_only' => 1
                    ]);
                }else{
                    $isApprovalOnly = ($divisionId == 2) ? 1 : 0;
                    $signatory = $data->signatories()->where('division_id', $divisionId)->first();
                    
                    if($signatory) {
                        if(($divisionId != 2)){
                            if($signatory->is_approval_only == 1){
                                $signatory->update([
                                    'is_approval_only' => 0, 
                                ]);
                            }
                        }
                    }elseif(!$signatory) {
                        $signatory = $data->signatories()->create([
                            'code' => $this->generateTravelSoloCode($divisionId,$data->type_id),
                            'division_id' => $divisionId,
                            'status_id' => ($isApprovalOnly) ? 25 : 24,
                            'is_approval_only' => $isApprovalOnly
                        ]);
                    }
                    $signatories[$divisionId] = $signatory->id;
                }
                $data->tags()->create([
                    'user_id' => intval($user['value']),
                    'division_id' => $divisionId,
                    'signatory_id' => $signatory->id, // Directly assign the signatory_id
                ]);
            }
            
            if(strpos($request->date, ' to ') !== false) {
                [$start, $end] = explode(' to ', $request->date);
            } else {
                $start = $end = $request->date;
            }

            $start = \Carbon\Carbon::parse($start)->toDateString();
            $end = \Carbon\Carbon::parse($end)->toDateString();

            $data->dates()->create([
                'start' => $start,
                'end' => $end,
                'time' => $request->time,
            ]);

            $data->detail()->create($request->only([
                'purpose', 'remarks'
            ]));
            $data->location()->create($request->only([
                'address','longitude','latitude','barangay_code','municipality_code','province_code','region_code'
            ]));

            $travelData = [
                'code' => $this->generateTravelCode(),
                'mode_id' => $request->mode_id,
                'transpo_id' => $request->transpo_id,
                'expense_id' => $request->expense_id,
                'expenses' => array_map('intval', $request->expenses)
            ];
            $travel = $data->travel()->create($travelData);
          
            if($request->mode_id == 150){
                $data->reservation()->create([
                    'vehicle_id' => $request->vehicle['value'],
                    'driver_id' => $request->vehicle['driver_id']
                ]);
                $signatory = $data->signatories()->where('division_id', $divisionId)->first();
                if(!$signatory) {
                    $signatory = $data->signatories()->create([
                        'code' => $this->generateTravelSoloCode($divisionId,$data->type_id),
                        'division_id' => $divisionId,
                        'status_id' => 24,
                        'is_approval_only' => 0
                    ]);
                }
                $data->tags()->create([
                    'user_id' => $request->vehicle['driver_id'],
                    'division_id' => 3,
                    'signatory_id' => $signatory->id,
                    'is_driver' => 1
                ]);
            }
        }

        return [
            'data' => $data,
            'message' => 'Travel Request Submitted', 
            'info' => "Your travel schedule has been submitted. Keep an eye on your notifications for any approvals or updates."
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

            $code = 'REQUEST-' . now()->format('mY') . '-TRAVEL-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            return $code;
        });
    }

    private function generateTravelCode()
    {
        return \DB::transaction(function () {
            $latest = RequestTravel::lockForUpdate()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->orderByDesc('id')
                ->first();

            $count = $latest
                ? (int) substr($latest->code, -4) + 1
                : 1;

            $code = 'TRVL-'.now()->format('Y') .'-'.str_pad($count, 4, '0', STR_PAD_LEFT);

            return $code;
        });
    }

    private function generateTravelSoloCode($divisionId,$type)
    {
        return \DB::transaction(function () use ($divisionId,$type) {
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
}
