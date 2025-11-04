<?php

namespace App\Http\Resources\Portal\Approval;

use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OvertimeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hashids = new Hashids('krad',10);
        $key = $hashids->encode($this->id);

        return [
            'id' => $this->id,
            'request_key' => $key,
            'request_id' => $this->id,
            'code' => $this->code,
            'type' => $this->type->name,
            'purpose' => $this->detail->purpose,
            'remarks' => $this->detail->remarks,
            'start' => $this->dates[0]->start,
            'end' => $this->dates[0]->end,
            'time' => $this->dates[0]->time,
            'status' => $this->status,
            'signatories' => count($this->signatories) === 1 
                    ? new SignatoryResource($this->signatories[0]) 
                    : SignatoryResource::collection($this->signatories),
            'employee' => $this->user->profile->firstname.' '.$this->user->profile->lastname,
            'tags' => TagResource::collection($this->tags),
            'comments' => CommentResource::collection($this->comments),
            'statuses' => StatusResource::collection($this->statuses),
            'overtime' => $this->overtime,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
