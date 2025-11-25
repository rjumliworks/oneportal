<?php

namespace App\Services\FAIMS\Procurement;
use App\Models\Procurement;
use App\Models\ProcurementQuotationItem;
use Illuminate\Support\Facades\Auth;

class OfferClass
{
    public function save($request){
        $item = ProcurementQuotationItem::with('quotation')->findOrFail($request->id);
        if($item){
            // update bid offer for bid_item
            $item->bid_price = $request->bid_price;
            $item->technical_proposal = $request->technical_proposal;
            $item->update();
        }

         $item->quotation->update([
            'delivery_term' => $request->delivery_term,
            
         ]);

        return [
            'data' => $item,
            'message' => 'Bid Offer updated successfuly!', 
            'info' => "You've successfully updated the Bid Offer.",
        ];
    }
    
  
    public function save_bid_for_award($request){
        $procurement = Procurement::with('status')->findOrFail($request->procurement_id);
        foreach ($request->items as $item) {
            $item = ProcurementQuotationItem::findOrFail($item['id']);
            if($item){
                // update item status to "Awarded" 
                $item->status_id = 43;
                $item->update();
            }
        }

        foreach ($request->itemsNotAvailableForAward as $item) {
            $item = ProcurementQuotationItem::findOrFail($item['id']);
            if($item){
                if (!empty($item->bid_price)) {
                    // update item status to "Available for Re-award" 
                    $item->status_id = 40;
                    $item->update();
                }
                else{
                    // update item status to "Not Available for Award/Re-award" 
                    $item->status_id = 41;
                    $item->update();
                }
             
            }
        }
        
        //dd($procurement->status->name);
        if($procurement && $procurement->status->name === 'Rebid'){
            // update PR status to "For BAC Resolution" 
            $procurement->update([
                'sub_status_id' => 44,
            ]);
        }
        else{
             // update PR status to "For BAC Resolution" 
            $procurement->update([
                'status_id' => 44,
            ]);
        }


        return [
            'data' => $request->items,
            'message' => 'Bid Items awarded successfuly!', 
            'info' => "You've successfully awarded the Bid Items.",
        ];
    }

    
   
}
