<?php

namespace App\Services\Portal\Approval;

use Hashids\Hashids;
use App\Models\Request;
use App\Models\RequestLeave;
use App\Http\Resources\Portal\Request\LeaveResource;
use App\Http\Resources\Portal\Request\OvertimeResource;

class ShowClass
{
    public function overtime($code){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($code);

        $data = Request::with([
            'tags.user:id',
            'tags.user.profile:user_id,firstname,middlename,lastname,suffix_id,avatar',
            'statuses.user:id',
            'statuses.user.profile:user_id,firstname,middlename,lastname,suffix_id,avatar',
            'statuses.status',
            'status',
            'type',
            'dates',
            'detail',
            'user:id',
            'overtime.status',
            'comments.user.profile:user_id,firstname,middlename,lastname,suffix_id,avatar',
            'comments.replies.user.profile:user_id,firstname,middlename,lastname,suffix_id,avatar',
            'user.profile:user_id,firstname,middlename,lastname,suffix_id',
            'signatories.division',
            'signatories.approved.user.profile','signatories.approved.signatory.designationable.designation',
            'signatories.recommended.user.profile','signatories.recommended.signatory.designationable.designation'
        ])
        ->where('id',$id)
        ->first();

        return new OvertimeResource($data);
    }

    public function leave($code){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($code);

        $data = RequestLeave::with([
            'detail',
            'type',
            'credits.log','credits.credit.leave',
            'request.comments.user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'request.comments.replies.user.profile:user_id,firstname,middlename,lastname,avatar,avatar,suffix_id',
            'request.tags.user:id',
            'request.tags.user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'request.statuses.user:id',
            'request.statuses.user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'request.statuses.status',
            'request.status',
            'request.type',
            'request.dates',
            'request.detail',
            'request.user:id',
            'request.user.profile:user_id,firstname,middlename,lastname,avatar,suffix_id',
            'request.signatories.division','request.signatories.approved','request.signatories.approved.signatory.designationable.designation','request.signatories.recommended','request.signatories.recommended.signatory.designationable.designation'
        ])
        ->where('request_id',$id)
        ->first();

        return new LeaveResource($data);
    }
}
