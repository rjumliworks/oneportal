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

        

        $procurement = Procurement::findOrFail($request->procurement_id);
        if($procurement){
             // update PR status to "For NOA" 
             $procurement->status_id = 45;
             $procurement->update();
        }

        return [
            'data' =>new ProcurementBacResource($data),
            'message' => 'BAC Resolution created successfully!', 
            'info' => "You've successfully added new BAC Resolution.",
        ];
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
        $bac_resolution = ProcurementBac::with('procurement')->findOrFail($id);

        $bac_resolution->update([
            'status_id' => 38, // set status to "Approved"
            'updated_by_id' => $user->id,
        ]);

        $bac_resolution->procurement->update([
            'status_id' => 46, // set status to "For NOA"
            'updated_by_id' => $user->id,
        ]);

        //save the items connected to this quotation that are awarded
        // dd($request->quotations);

        // create NOA and its items
        $this->createNOA($request, $bac_resolution, $user);


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
