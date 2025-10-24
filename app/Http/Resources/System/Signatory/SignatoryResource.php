<?php

namespace App\Http\Resources\System\Signatory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignatoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'is_oic' => $this->is_oic,
            'user' => new ProfileResource($this->user),
            'oic' => new ProfileResource($this->oic),
            'schedules' => ScheduleResource::collection($this->schedules)
        ];
    }
}
