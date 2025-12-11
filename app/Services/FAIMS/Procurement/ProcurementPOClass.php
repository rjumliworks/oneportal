<?php

namespace App\Services\FAIMS\Procurement;

use App\Models\ProcurementBac;
use App\Models\Procurement;
use App\Models\ProcurementBacNoa;
use App\Models\ProcurementNoaPo;
use App\Models\ProcurementPoNtp;

use App\Http\Resources\FAIMS\Procurement\ProcurementNoaPoResource;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProcurementPOClass
{
    public function lists($request)
    {
        $data = ProcurementNoaPoResource::collection(
            ProcurementNoaPo::with('noa')
                ->when($request->keyword, function ($query) use ($request) {
                    $keyword = $request->keyword;

                    $query->where(function ($q) use ($keyword) {
                        $q->where('code', 'LIKE', "%{$keyword}%")
                        ->orWhereHas('noa', function ($noaQ) use ($keyword) {
                            $noaQ->where('code', 'LIKE', "%{$keyword}%");
                        });
                    });
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status_id', $request->status);
                })
                ->orderBy('created_at','DESC')
                ->paginate($request->count)
        );

        return $data;
    }


    public function purchase_order($request){
        $data =  ProcurementNoaPo::with('status')->where('noa_id', $request->noa_id)->first();
        return $data;
    }

    public function save($request)
    { 
        //dd($request->all());
        $user = Auth::user();
        $code = ProcurementNoaPo::generatePONumber();

        $data = ProcurementNoaPo::create([
            'code' => $code,
            'po_date' => now()->toDateString(),
            'payment_term' => $request->payment_term,
            'delivery_term' => $request->delivery_term,
            'noa_id' => $request->noa_id,
            'place_of_delivery_id' => $request->place_of_delivery_id,
            'date_of_delivery' => $request->date_of_delivery,
            'created_by_id' => $user->id,
            'status_id' => 36, // set to "pending"
        ]);


        $noa = ProcurementBacNoa::with('procurement_bac.procurement')->findOrFail($request->noa_id);
        
        if($noa){
             // update PR status to "PO Pending" 
             $noa->status_id = 62;
             $noa->update();
        }


        return [
            'data' =>new ProcurementNoaPoResource($data),
            'message' => 'BAC Resolution created successfully!', 
            'info' => "You've successfully added new BAC Resolution.",
        ];
    }

    
    public function update($id, $request)
    { 
        $user = Auth::user();
        $data = ProcurementNoaPo::findOrFail($id);

        $data->update([
            'body' => $request->body,
            'updated_by_id' => $user->id,
        ]);

        return [
            'data' =>new ProcurementNoaPoResource($data),
            'message' => 'BAC Resolution created successfully!', 
            'info' => "You've successfully added new BAC Resolution.",
        ];
    }

       
    public function updateStatus($id, $request)
    { 
 
        $user = Auth::user();
        $po = ProcurementNoaPo::with('noa.procurement_bac.procurement')->findOrFail($id);

        if($request->status['name'] == "Pending"){

             $po->update([
                'status_id' => 47, // set status to "served to supplier"
            ]);
            $po->noa->update([
                'status_id' => 49, // set noa status to "PO Served to Supplier"
            ]);
        }
        else if($request->status['name'] == "Served to Supplier"){
       
            $po->update([
                'status_id' => 54, // set status to "Conformed"  
            ]);

            $po->noa->update([
                'status_id' => 51, // set noa status to "PO Conformed"
            ]);

            // create Notice to Proceed(NTP) 
            $this->createNTP($request, $po, $user);
        }
        else if($request->status['name'] == "Conformed"){
             $po->update([
                'status_id' => 52, // set status to "Delivered/For Inspection"
               
            ]);
            $po->noa->update([
                'status_id' => 52, // set noa status to "PO Delivered/For Inspection"
            ]);
        }
        else if($request->status['name'] == "Delivered/For Inspection"){
             $po->update([
                'status_id' => 53, // set status to "Completed"
               
            ]);
            $po->noa->update([
                'status_id' => 53, // set status to "Completed"
            ]);
        }
        $current_pr_status = $po->noa->procurement_bac->procurement->status_id;
        $procurement =  $po->noa->procurement_bac->procurement;

        // if current_pr_status "Re-award" or "Rebid"
       if($current_pr_status == 59 || $current_pr_status == 60){
            $updated_pr_substatus = $po->noa->procurement_bac->overall_substatus($current_pr_status);
            // update Procurement Request Status
            $procurement->update([
                'sub_status_id' =>  $updated_pr_substatus,
            ]);

            // if pr sub_status is "Completed" update pr status also to "Completed"
            if($updated_pr_substatus == 53){
                $procurement->update([
                    'status_id' =>  $updated_pr_substatus,
                ]);
            }
        }
        else{
            $updated_pr_status = $po->noa->procurement_bac->overall_status($current_pr_status);
            // update Procurement Request Status
            $procurement->update([
                'status_id' =>  $updated_pr_status,
            ]);
        }

        return [
            'data' =>new ProcurementNoaPoResource($po),
            'message' => 'Purchase Order Status updated successfully!', 
            'info' => "You've successfully updated Purchase Order Status.",
        ];
    }


    public function createNTP($request, $po , $user)
    { 
        // Loop through each awarded quotation
            $code = ProcurementPoNtp::generateNTPNumber();
            $noa = ProcurementPoNtp::create([
                'code' => $code,
                'po_id' => $po->id,
                'created_by_id' => $user->id,
                'status_id' => 36, //set status to "pending"
            ]);
    }


    public function notConformed($id, $request)
    { 
        $user = Auth::user();
        $po = ProcurementNoaPo::with('noa.procurement_bac.procurement' , 'status')->findOrFail($id);

        $po->update([
            'status_id' => 55, // set status to "not conformed"
        ]);

        $po->noa->update([
            'status_id' => 61, // set noa status to "PO Not Conformed"
        ]);

        $po->noa->procurement_bac->update([
            'status_id' => 61, // set bac resolution status to "PO Not Conformed"
        ]);
    
        $po->comments()->create([
            'content' => $request->comment,
            'user_id' => $user->id, 
        ]);

        $procurement = $po->noa->procurement_bac->procurement;
        $current_pr_status = $po->noa->procurement_bac->procurement->status_id;

        // if updated status is "re-award" or "rebid"
        if($current_pr_status  == 59 || $current_pr_status  == 60){
            $updated_pr_substatus = $po->noa->procurement_bac->overall_substatus($current_pr_status);
            if($updated_pr_substatus  == 59){
                $procurement->update([
                    'reawarded_count' =>  $procurement->reawarded_count + 1,
                    'sub_status_id' => null,
                ]);
            }
            else if($updated_pr_substatus  == 60){
                $procurement->update([
                    'rebidded_count' =>  $procurement->rebidded_count + 1,
                   'sub_status_id' => null,
                ]);
            }
            // --- update the old BAC Resolution status to "PO Not Conformed" --
            $po->noa->update([
                'status_id' => 61,
            ]);

        }
        else{
            $updated_pr_status = $po->noa->procurement_bac->overall_status($current_pr_status);
            if($updated_pr_status  == 59){
                $procurement->update([
                    'reawarded_count' =>  $procurement->reawarded_count + 1,
                    'status_id' =>  $updated_pr_status,
                ]);
            }
            else if($updated_pr_status  == 60){
                $procurement->update([
                    'rebidded_count' =>  $procurement->rebidded_count + 1,
                    'status_id' =>  $updated_pr_status,
                ]);
            }
            //--- update the old BAC Resolution status to "PO Not Conformed" --
            $po->noa->update([
                'status_id' => 61,
            ]);
        }
        return [
            'data' =>new ProcurementNoaPoResource($po),
            'message' => 'Purchase Order Status updated successfully!', 
            'info' => "You've successfully updated Purchase Order Status.",
        ];
    }



   

}
