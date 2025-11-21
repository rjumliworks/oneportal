<?php

namespace App\Services\FAIMS\Procurement;

use App\Models\Procurement;
use App\Models\ProcurementQuotation;
use App\Models\ProcurementQuotationItem;
use App\Http\Resources\FAIMS\Procurement\ProcurementQuotationResource;
use Illuminate\Support\Facades\Auth;

class ProcurementQuotationClass
{
    public function save($request){
        // create initial 
        foreach ($request->supplier_ids as $supplier_id) {

            // save Request for Quotation(RFQ)
            $code= ProcurementQuotation::generateRFQNumber();
            $procurement_quotation = new ProcurementQuotation();
            $procurement_quotation->code = $code;
            $procurement_quotation->procurement_id = $request->procurement_id;
            $procurement_quotation->submission_not_later_than = $request->submission_not_later_than;
            $procurement_quotation->supply_officer_id = $request->supply_officer_id;
            $procurement_quotation->supplier_id = $supplier_id;
            $procurement_quotation->status_id = 36; // set to 'pending' 
            $procurement_quotation->save();

            // items
            foreach ($request->items as $item) {
                // create initial        
                $procurement_quotation_item = new ProcurementQuotationItem();
                $procurement_quotation_item->quotation_id = $procurement_quotation->id;
                $procurement_quotation_item->procurement_item_id = $item['id'];
                $procurement_quotation_item->status_id = 39; // set status to "available for award"
                $procurement_quotation_item->save();

            }
        }

        $procurement = Procurement::findOrFail($request->procurement_id);
        // update Procurement status
        $procurement->quotation_count = $procurement->quotation_count+1;
        $procurement->status_id = 42; // set to "For Bids"
        $procurement->update();

        return [
            'data' => new ProcurementQuotationResource($procurement_quotation),
            'message' => 'Request for Quotations successfuly saved!', 
            'info' => "You've successfully created the Request for Quotation.",
        ];
    }

    // public function destroy($id){
    //     // Find the RFQ by ID
    //     $quotation_request = QuotationRequest::findOrFail($id);

    //     // Delete the RFQ
    //     $quotation_request->delete();
        
    //     return [
    //         'data' => new QuotationRequestResource($quotation_request),
    //         'message' => 'Request for Quotations successfuly deleted!', 
    //         'info' => "You've successfully deleted the Request for Quotation.",
    //     ];
    // }

    // public function list_of_existed_rfq($request){
     
    //     // get the latest RFQ created
    //     $list_of_existed_rfq = QuotationRequest::where('purchase_request_id', $request->purchase_request_id)
    //     ->orderBy('id', 'desc')
    //     ->get()->map(function ($item) {
    //         return [
    //             'value' => $item->supplier_id,
    //             'name' => $item->supplier->name,
    //         ];
    //     });;

    //     return  $list_of_existed_rfq;


    // }

    // public function print($id, $request){
    //     $date = now()->format('d F Y');

    //     $item_details = PurchaseRequestDetail::with('unit_type')->where('purchase_request_id', $request->purchase_request_id)->get();
    //     $supplier = Supplier::findOrFail($request->supplier_id);

    //     $supply_officer = UserProfile::findOrFail($request->supplier_officer_id);

    //     $data = QuotationRequest::findOrFail($id);

    //     $array = [
    //         'data' => $data,
    //         'supplier' => $supplier,
    //         'supply_officer' =>  $supply_officer,
    //         'submission_not_later_than' =>  (new \DateTime($data->submission_not_later_than))->format('F d, Y'),
    //         'date' =>  $date,
    //         'rfq_number' => $request->rfq_no,
    //         'purchase_request_number' => $request->purchase_request_number,
    //         'item_details' =>  $item_details,
    //     ];

    //     $pdf = \PDF::loadView('FAIMS.Procurement.printQuotation',$array)->setPaper('A4', 'portrait');
    //     return $pdf->stream($request->purchase_request_number.'.pdf');
    // }
}
