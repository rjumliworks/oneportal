<?php

namespace App\Services\FAIMS\Procurement;

use App\Models\ProcurementBac;
use App\Models\Procurement;
use App\Models\ProcurementBacNoa;

use App\Http\Resources\FAIMS\Procurement\ProcurementBacNoaResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class ProcurementBacNoaClass
{
    public function lists($request){
        $data = ProcurementBacNoaResource::collection(
            ProcurementBacNoa::with('procurement_bac.procurement', 'procurement_quotation.supplier')
            ->when($request->procurement_id, function ($query, $procurement_id) {
                $query->where('procurement_id', $procurement_id);
            })
            ->when($request->bac_resolution_id, function ($query, $bac_resolution_id) {
                $query->where('procurement_bac_id', $bac_resolution_id);
            })
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('code', 'LIKE', "%{$keyword}%")
                      ->orWhereHas('procurement', function ($q) use ($keyword) {
                        $q->where('code', 'LIKE', "%{$keyword}%");
                      })
                      ->orWhereHas('procurement_quotation.supplier', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%{$keyword}%");
                      });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status_id', $status);
            })
            ->orderBy('created_at','DESC')
            ->paginate($request->count)
        );

        return $data;
    }

       
    public function updateStatus($id, $request)
    {
        $user = Auth::user();
        $noa = ProcurementBacNoa::with('procurement_bac.procurement' , 'status')->findOrFail($id);

        // Get current status name
        $currentStatusName = is_array($request->status) ? $request->status['name'] : $request->status->name;
   
        if($currentStatusName == "Pending"){
            $noa->update([
                    'status_id' => 47, // set status to "served to supplier"
            ]);
            activity()->performedOn($noa)->causedBy($user)->log('NOA status updated to Served to Supplier');
        }
        elseif($currentStatusName == "Served to Supplier"){
            $noa->update([
                'status_id' => 54, // set status to "conformed"
            ]);
            activity()->performedOn($noa)->causedBy($user)->log('NOA status updated to Conformed');
        }
        elseif($currentStatusName == "Conformed"){
            $noa->update([
                'status_id' => 52, // set status to "delivered/for inspection"
            ]);
            activity()->performedOn($noa)->causedBy($user)->log('NOA status updated to Delivered/For Inspection');
        }

        $current_pr_status = $noa->procurement_bac->procurement->status_id;
        $procurement = $noa->procurement_bac->procurement;

        // if current_pr_status "Re-award" or  "Rebid"
        if($current_pr_status == 59  || $current_pr_status == 60){
            $updated_pr_substatus = $noa->procurement_bac->overall_substatus($current_pr_status);
            // update Procurement Request Status
            $noa->procurement_bac->procurement->update([
                'sub_status_id' =>  $updated_pr_substatus,
            ]);

        }
        else{
            $updated_pr_status = $noa->procurement_bac->overall_status($current_pr_status);
     
            // update Procurement Request Status
            $noa->procurement_bac->procurement->update([
                'status_id' =>  $updated_pr_status,
            ]);
        }
        
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

        activity()->performedOn($noa)->causedBy($user)->log('NOA status updated to Not Conformed and '. $user->profile->fullname . ' commented '. $request->comment);

        $noa->comments()->create([
            'content' => $request->comment,
            'user_id' => $user->id,
        ]);
        
        $procurement = $noa->procurement_bac->procurement;
        $current_pr_status = $noa->procurement_bac->procurement->status_id;
        $updated_pr_status = $noa->procurement_bac->overall_status($current_pr_status);
    
        // if updated status is "re-award" or "rebid"
        if($updated_pr_status  == 59 || $updated_pr_status  == 60){
            if($updated_pr_status  == 59){
                $procurement->update([
                    'sub_status_id' => null,
                    'reawarded_count' =>  $procurement->reawarded_count + 1,
                ]);
            }
            else if($updated_pr_status  == 60){
                $procurement->update([
                    'sub_status_id' => null,
                    'rebidded_count' =>  $procurement->rebidded_count + 1,
                ]);
            }
            // --- update the old BAC Resolution status to "NOA Not Conformed" -----
            $noa->procurement_bac->update([
                'status_id' => 68,
            ]);
        }

        // update Procurement Request Status
        $procurement->update([
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
