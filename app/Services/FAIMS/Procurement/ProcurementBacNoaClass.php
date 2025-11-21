<?php

namespace App\Services\FAIMS\Procurement;

use App\Models\ProcurementBac;
use App\Models\Procurement;
use App\Models\ProcurementBacNoa;

use App\Http\Resources\FAIMS\Procurement\ProcurementBacNoaResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProcurementBacNoaClass
{
    public function lists($request){
        $data = ProcurementBacNoaResource::collection(
            ProcurementBacNoa::query()
            ->where('procurement_bac_id', $request->bac_resolution_id)
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('code', 'LIKE', "%{$keyword}%");
            })
            ->orderBy('created_at','DESC')
            ->paginate($request->count)
        );

        return $data;
    }

       
    public function updateStatus($id, $request)
    { 
        $user = Auth::user();
        $noa = ProcurementBacNoa::with('procurement_bac.procurement')->findOrFail($id);

        if($request->status['name'] == "Pending"){
             $noa->update([
                'status_id' => 47, // set status to "served to supplier"
                'updated_by_id' => $user->id,
            ]);
        }
        else{
            $noa->update([
                'status_id' => 54, // set status to "conformed"
                'updated_by_id' => $user->id,
            ]);

        }
      
        $current_pr_status = $noa->procurement_bac->procurement->status_id;
        $updated_pr_status = $noa->procurement_bac->overall_status($current_pr_status);

        // update Procurement Request Status
        $noa->procurement_bac->procurement->update([
            'status_id' =>  $updated_pr_status,
            'updated_by_id' => $user->id,
        ]); 

        return [
            'data' =>new ProcurementBacNoaResource($noa),
            'message' => 'NOA Status updated successfully!', 
            'info' => "You've successfully updated NOA Status.",
        ];
    }


    public function notConformed($id, $request)
    { 
        $user = Auth::user();
        $noa = ProcurementBacNoa::with('procurement_bac.procurement')->findOrFail($id);

        $noa->update([
            'status_id' => 55, // set status to "not conformed"
            'updated_by_id' => $user->id,
        ]);

        $noa->comments()->create([
            'content' => $request->comment,
            'user_id' => $user->id, 
        ]);
        
         
        $current_pr_status = $noa->procurement_bac->procurement->status_id;
        $updated_pr_status = $noa->procurement_bac->overall_status($current_pr_status);

        // update Procurement Request Status
        $noa->procurement_bac->procurement->update([
            'status_id' => $updated_pr_status,
            'updated_by_id' => $user->id,
        ]); 

        return [
            'data' =>new ProcurementBacNoaResource($noa),
            'message' => 'BAC Resolution Status updated successfully!', 
            'info' => "You've successfully updated BAC Resolution Status.",
        ];
    }

   
}
