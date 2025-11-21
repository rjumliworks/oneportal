<?php

namespace App\Services\FAIMS\Procurement;

use App\Models\ProcurementCode;
use App\Models\ProcurementCodeUnit;
use App\Http\Resources\FAIMS\Procurement\ProcurementCodeResource;
use Illuminate\Support\Facades\Auth;

class ProcurementCodeClass
{
    public function lists($request){
        $data = ProcurementCodeResource::collection(
            ProcurementCode::query()
            ->with('end_users.end_user')
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")
                        ->orWhere('code', 'LIKE', "%{$keyword}%");
            })
            ->orderBy('created_at','DESC')
            ->paginate($request->count)
        );
        return $data;
    }

    public function save($request)
    {
        // Create the PAP Code with the correct syntax
        $procurement_code = ProcurementCode::create($request->only(
                'title', 
                'code', 
                'year', 
                'allocated_budget',
                'app_type_id',
                'mode_of_procurement_id'
            )
        );

        // Loop through end_user_ids and save them
        foreach ($request->end_user_ids as $end_user_id) {
            ProcurementCodeUnit::create([
                'procurement_code_id' => $procurement_code->id,
                'end_user_id' => $end_user_id,
            ]);
        }

        // Wrap the newly created PAPCode in a Resource
        return [
            'data' => new ProcurementCodeResource($procurement_code),
            'message' => 'PAP Code created successfully!',
            'info' => "You've successfully added new PAP Code.",
        ];
    }

}
