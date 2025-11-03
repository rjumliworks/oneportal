<?php

namespace App\Http\Resources\FAIMS\Procurement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class ProcurementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' =>  $this->code,
            'date' => $this->date,
            'purpose' =>  $this->purpose,
            'title' =>  $this->title,
            'unit' =>  $this->unit,
            'division' =>  $this->division,
            'fund_cluster' =>  $this->fund_cluster,
            'requested_by' => $this->requested_by->profile->full_name ,
            'approved_by' =>  $this->approved_by,
            'procurement_codes' =>  $this->procurement_codes,
            'quotation_count'  => $this->quotation_count,
            'reawarded_count'  => $this->reawarded_count,
            'rebidded_count'  => $this->rebidded_count,
            'status' =>  $this->status,
        ];
    }
}
