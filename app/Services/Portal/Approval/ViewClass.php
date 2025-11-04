<?php

namespace App\Services\Portal\Approval;

use App\Models\Request;
use App\Models\RequestSignatory;
use App\Models\OrgSignatory;
use App\Http\Resources\Portal\Approval\IndexResource;

class ViewClass
{
    public function lists($request){
        $signatory = OrgSignatory::with('designationable')->where('user_id',\Auth::user()->id)->where('is_active',1)->first(); 
        $status = $request->status ?? (($signatory['designationable']['designation_id'] == 44) ? 24 : 25);
        $data = RequestSignatory::with([
            'status',
            'statusable',
            'request.tags.user:id',
            'request.tags.user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'request.status',
            'request.type',
            'request.dates',
            'request.detail',
            'request.travel', // add relation to request_travel
            'request.travel.codes', // nested relation to request_travel_code
        ])
        ->when($request->type, fn($q, $expense) => $q->where('type_id', $expense))
        ->when($request->keyword, function ($query, $keyword) {
            $query->whereHas('user.profile', function ($q) use ($keyword) {
                $q->whereRaw('LOWER(lastname) LIKE ?', ['%' . strtolower($keyword) . '%']);
            });
        })
        ->when(empty($request->status), fn($q) => $q->where('status_id', $status))
        ->when(true, function ($query) use ($signatory, $request) {
            // previously inside whereHas('signatories')
            if ($request->status) {
                if ($request->status == 26) {
                    $query->where('approved_id', \Auth::user()->id);
                } else {
                    $query->where('recommended_id', \Auth::user()->id);
                }
            } else {
                if ($signatory['designationable']['designation_id'] == 44) {
                    $query->where('division_id', $signatory['designationable']['assigned_id'])
                          ->where('is_approval_only', 0);
                }
            }
        })
        ->orderBy('created_at','ASC')
        ->paginate($request->count ?? 10);

        return IndexResource::collection($data);
    }

    public function count(){
        return [
            Request::whereHas('signatories', function ($query) { $query->where('recommended_id', \Auth::user()->id); })->count(),
            Request::whereHas('signatories', function ($query) { $query->where('approved_id', \Auth::user()->id); })->count(),
        ];
    }
}
