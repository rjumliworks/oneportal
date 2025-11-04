<?php

namespace App\Http\Resources\Portal\Request;

use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hashids = new Hashids('krad',10);
        $key = $hashids->encode($this->id);
        $request_key = $hashids->encode($this->request_id);

        return [
            'id' => $this->id,
            'key' => $key,
            'request_key' => $request_key,
            'request_id' => $this->request->id,
            'code' => $this->request->code,
            'type' => $this->request->type->name,
            'purpose' => $this->request->detail->purpose,
            'remarks' => $this->request->detail->remarks,
            'start' => $this->request->dates[0]->start,
            'end' => $this->request->dates[0]->end,
            'time' => $this->request->dates[0]->time,
            'status' => $this->request->status,
            'employee' => $this->request->user->profile->firstname.' '.$this->request->user->profile->lastname,
            'mode' => $this->mode,
            'tags' => TagResource::collection($this->request->tags),
            'expense' => $this->expense,
            'expenses' => $this->expense_items, 
            'codes' => $this->codes,
            'comments' => CommentResource::collection($this->request->comments),
            'signatories' => $this->request->signatories,
            'statuses' => StatusResource::collection($this->request->statuses),
            'location' => new LocationResource($this->request->location),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
