<?php

namespace App\Http\Resources\Portal\Request;

use Hashids\Hashids;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hashids = new Hashids('krad',10);
        $key = $hashids->encode($this->id);

        switch($this->type->name){
            case 'Travel Order':
                $subtype = $this->travel->mode->name;
            break;
            case 'Leave Form':
                $subtype = $this->leave->type->name;
            break;
            case 'Render Overtime Service':
                $subtype = $this->type->name;
            break;
            default:
                $subtype = $this->reservation->vehicle->name;
        }

        $link = Str::slug($this->type->name) . 'krad' . $key;

        return [
            'id' => $this->id,
            'key' => $key,
            'code' => $this->code,
            'type' => $this->type->name,
            'link' => Crypt::encryptString($link),
            'purpose' => $this->detail->purpose,
            'remarks' => $this->detail->remarks,
            'start' => $this->dates[0]->start,
            'end' => $this->dates[0]->end,
            'status' => $this->status,
            'tags' => TagResource::collection($this->tags),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'subtype' => $subtype
        ];
    }
}
