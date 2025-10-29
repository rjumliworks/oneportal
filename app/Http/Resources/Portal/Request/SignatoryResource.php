<?php

namespace App\Http\Resources\Portal\Request;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignatoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'recommended' => $this->recommended ? ($this->recommended->profile->name) : null,
            'recommended_date' => $this->recommended_date,
            'recommended_by' => asset('storage/'.$this->recommended_by),
            'approved' => $this->approved ? ($this->approved->profile->name) : null,
            'approved_date' => $this->approved_date,
            'approved_by' => asset('storage/'.$this->approved_by),
            'is_disapproved' => $this->is_disapproved,
            'is_approval_only' => $this->is_approval_only
        ];
    }
}
