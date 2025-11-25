<?php

namespace App\Services\FAIMS\Procurement;

use App\Models\ProcurementBac;
use App\Models\Procurement;
use App\Models\ProcurementBacNoa;
use App\Models\ProcurementBacNoaItem;
use App\Models\ProcurementQuotation;



use App\Http\Resources\FAIMS\Procurement\ProcurementBacResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProcurementBACClass
{
    public function lists($request){
        $data = ProcurementBacResource::collection(
            ProcurementBac::query()
            ->where('procurement_id', $request->procurement_id)
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('code', 'LIKE', "%{$keyword}%");
            })
            ->orderBy('created_at','DESC')
            ->paginate($request->count)
        );

        return $data;
    }

    public function save($request)
    { 
        $procurement = Procurement::with('status', 'sub_status')->findOrFail($request->procurement_id);

        switch($request->type){
            case 'Award':
                 $this->saveAwardBACResolution( $procurement, $request);
            break;
            case 'Rebid':
                $this->saveFailureBACResolution( $procurement, $request);
            break;
            case 'Re-award':
                dd('re-award'); 
            break;
        }

        if($procurement){
            $user = Auth::user();
            $code = ProcurementBac::generateBACResolutionNumber();
            $data = ProcurementBac::create([
                'code' => $code,
                'type' => $request->type,
                'body' => $request->body,
                'procurement_id' => $request->procurement_id,
                'created_by_id' => $user->id,
                'status_id' => 36, // set to "pending"
            ]);    
        }

        return [
            'data' =>new ProcurementBacResource($data),
            'message' => 'BAC Resolution created successfully!', 
            'info' => "You've successfully added new BAC Resolution.",
        ];
    }

    protected function saveAwardBACResolution($procurement, $request)
    { 
        if($procurement->status->name === "Rebid"){
            $procurement->update([
                'sub_status_id' => 45  ,//set to 'For Bids'
            ]);   
        }
        else{
            // update procurement substatus to "For Quotations"
            $procurement->update([
                'status_id' => 45  ,//set to 'For Approval of Failure BAC Resolution'
            ]);   
        }
          

    }

    protected function saveReAwardBACResolution($procurement, $request)
    { 
        if($procurement->status->name === "Re-award"){
            $procurement->update([
                'sub_status_id' => 45  ,//set to 'For Bids'
            ]);   
        }
        else{
            // update procurement substatus to "For Quotations"
            $procurement->update([
                'status_id' => 45  ,//set to 'For Approval of Failure BAC Resolution'
            ]);   
        }
          

    }

    protected function saveFailureBACResolution($procurement, $request)
    { 

        // update procurement substatus to "For Quotations"
        $procurement->update([
            'sub_status_id' => 72  ,//set to 'For Approval of Failure BAC Resolution'
        ]);     
        // Update related RFQs to "Failed RFQs"
        $procurement->quotations()->update([
            'status_id' => 71, // Failed RFQ
        ]);
        // Update related items of each quotation where item status is "Awarded" (status_id = 43)
        foreach ($procurement->quotations->where('status_id', 71) as $quotation) {
            foreach ($quotation->items->where('status_id', 43) as $item) {
                $item->update([
                    'is_rebid' => 1, // so that next bac resolution type "Award" will not show the items past awarded
                ]);
            }
        }
        // Update related BAC Resolutions to "Failed Biddings"
        $procurement->bac_resolutions()->update([
            'status_id' => 70, //  set to "Failed Bidding"
        ]);
    }
    
    public function update($id, $request)
    { 
        $user = Auth::user();
        $data = ProcurementBac::findOrFail($id);

        $data->update([
            'body' => $request->body,
            'updated_by_id' => $user->id,
        ]);

        return [
            'data' =>new ProcurementBacResource($data),
            'message' => 'BAC Resolution created successfully!', 
            'info' => "You've successfully added new BAC Resolution.",
        ];
    }

       
    public function updateStatus($id, $request)
    { 
        $user = Auth::user();
        $bac_resolution = ProcurementBac::with('procurement.status' )->findOrFail($id);

        $bac_resolution->update([
            'status_id' => 38, // set status to "Approved"
        ]);

        $procurement = $bac_resolution->procurement;
        // if status is "re-award"
        if( $procurement->status->name == "Re-award"){
             $procurement->update([
                'sub_status_id' => 46, // set sub_status to "For NOA"
            ]);

        }
        else if( $procurement->status->name == "Rebid"){
             $procurement->update([
                'sub_status_id' => 46, // set sub_status to "For NOA"
            ]);
        }
        else{
            $procurement->update([
                'status_id' => 46, // set status to "For NOA"
            ]);
        }

        if($request->type != "Rebid"){
             // create NOA and its items
            $this->createNOA($request, $bac_resolution, $user);
        }
   
        
        return [
            'data' =>new ProcurementBacResource($bac_resolution),
            'message' => 'BAC Resolution Status updated successfully!', 
            'info' => "You've successfully updated BAC Resolution Status.",
        ];
    }


    public function createNOA($request, $bac_resolution , $user)
    { 
        // Loop through each awarded quotation
        foreach ($request->quotations as $quotation) {
            $code = ProcurementBacNoa::generateNOANumber();
            $noa = ProcurementBacNoa::create([
                'code' => $code,
                'procurement_bac_id' => $bac_resolution->id,
                'procurement_quotation_id' => $quotation['id'],
                'created_by_id' => $user->id,
                'status_id' => 36, //set to "pending"
            ]);

            // create noa items
            foreach ($quotation['items'] as $item) {
                if(!empty($item['bid_price']) ) // if item status is "Awarded" or "Available for re-award"
                {
                    ProcurementBacNoaItem::create([
                        'procurement_bac_noa_id' => $noa->id,
                        'item_id' => $item['id'],
                    ]);

                }
            }
        }
    }




        



    


    

   
}
