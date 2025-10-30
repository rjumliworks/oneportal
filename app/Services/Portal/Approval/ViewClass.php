<?php

namespace App\Services\Portal\Approval;

use App\Models\Request;
use App\Models\OrgSignatory;
use App\Http\Resources\Portal\Request\IndexResource;

class ViewClass
{
    public function lists($request){
        $signatory = OrgSignatory::with('designationable')->where('user_id',\Auth::user()->id)->where('is_active',1)->first(); 
        $status = $request->status ?? (($signatory['designationable']['designation_id'] == 44) ? 24 : 25);
        $data = Request::with([
            'tags.user:id',
            'tags.user.profile:user_id,firstname,middlename,lastname,avatar',
            'status',
            'type',
            'dates',
            'detail',
        ])
        ->when($request->type, fn($q, $expense) => $q->where('type_id', $expense))
        ->when($request->keyword, function ($query, $keyword) {
            $query->whereHas('user.profile', function ($q) use ($keyword) {
                $q->whereRaw('LOWER(lastname) LIKE ?', ['%' . strtolower($keyword) . '%']);
            });
        })
        ->when(empty($request->status), fn($q) => $q->where('status_id', $status))
        ->whereHas('signatories', function ($query) use ($signatory,$request) {
            if($request->status){
                if($request->status == 26){
                    $query->where('approved_id', \Auth::user()->id);
                }else{
                    $query->where('recommended_id', \Auth::user()->id);
                }
            }else{
                if ($signatory['designationable']['designation_id'] == 44){
                    $query->where('division_id', $signatory['designationable']['assigned_id']);
                    $query->where('is_approval_only',0);
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
