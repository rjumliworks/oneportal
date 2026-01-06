<?php

namespace App\Services\FAIMS\Procurement;

use App\Services\DropdownClass;
use App\Models\Procurement;
use App\Models\ProcurementQuotation;
use App\Models\ProcurementBac;
use App\Models\ProcurementBacNoa;
use App\Models\ProcurementNoaPo;
use App\Models\ProcurementPoNtp;
use Spatie\Activitylog\Models\Activity;

use App\Http\Resources\FAIMS\Procurement\ProcurementResource;
use App\Http\Resources\FAIMS\Procurement\ProcurementQuotationResource;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;



class ViewClass
{
    public function __construct( DropdownClass $dropdown){
        $this->dropdown = $dropdown;
    }

    public function procurements($request){
        $data = ProcurementResource::collection(
            Procurement::with('status')
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('code', 'LIKE', "%{$keyword}%")
                      ->orWhere('date', 'LIKE', "%{$keyword}%")
                      ->orWhere('created_at', 'LIKE', "%{$keyword}%")
                      ->orWhere('updated_at', 'LIKE', "%{$keyword}%");
            })
            ->orderBy('created_at','DESC')
            ->paginate($request->count)
        );
        return $data;
    }

    public function quotations($request){
        $data = ProcurementQuotationResource::collection(
            ProcurementQuotation::query()
            ->with('supplier.address' ,'supply_officer')
            ->when($request->procurement_id, function ($query, $procurement_id) {
                $query->where('procurement_id', $procurement_id);
            })
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('code', 'LIKE', "%{$keyword}%")
                      ->orWhere('date', 'LIKE', "%{$keyword}%")
                       ->orWhereHas('supplier',function ($query) use ($keyword) {
                            $query->where('name', 'LIKE', "%{$keyword}%");
                        })->orWhereHas('supply_officer',function ($query) use ($keyword) {
                            $query->where('name', 'LIKE', "%{$keyword}%");
                        })  
                        ->orWhere('created_at', 'LIKE', "%{$keyword}%")
                      ->orWhere('updated_at', 'LIKE', "%{$keyword}%");
            })
            ->orderBy('created_at','DESC')
            ->paginate($request->count)
        );

        return $data;
    }


    public function show($id, $request){

        $procurement = Procurement::with('division','unit', 'codes' , 'items' , 'approved_by.profile' , 'items.item_unit_type', 'quotations.supplier' ,  'quotations.items' , 'status', 'sub_status' , 'requested_by', 'created_by'
                                    , 'bac_resolutions', 'comments.user.profile', 'comments.replies.user.profile')->findOrFail($id);

        $logs = Activity::where(function ($query) use ($id) {
            $query->where('subject_type', Procurement::class)
                  ->where('subject_id', $id);
                    })->orWhere(function ($query) use ($id) {
                        $query->where('subject_type', ProcurementQuotation::class)
                            ->whereIn('subject_id', ProcurementQuotation::where('procurement_id', $id)->pluck('id'));
                    })->orWhere(function ($query) use ($id) {
                        $query->where('subject_type', ProcurementBac::class)
                            ->whereIn('subject_id', ProcurementBac::where('procurement_id', $id)->pluck('id'));
                    })->orWhere(function ($query) use ($id) {
                        $query->where('subject_type', ProcurementBacNoa::class)
                            ->whereIn('subject_id', ProcurementBacNoa::where('procurement_id', $id)->pluck('id'));
                    })->orWhere(function ($query) use ($id) {
                        $query->where('subject_type', ProcurementNoaPo::class)
                            ->whereIn('subject_id', ProcurementNoaPo::where('procurement_id', $id)->pluck('id'));
                    })->with('causer')->orderBy('created_at', 'desc')->get();
                    switch($request->option){
            
            case 'view':
                 return inertia('Modules/FAIMS/Procurement/View', [
                    'dropdowns' => [
                        'divisions' => $this->dropdown->dropdowns('Division'),
                        'fund_clusters' => $this->dropdown->dropdowns('Fund Cluster'),
                        'procurement_codes' => $this->dropdown->procurement_codes(),
                        'unit_types' => $this->dropdown->unit_types(),
                        'requesters' => $this->dropdown->requesters(),
                        'approvers' => $this->dropdown->approvers(),
                        'supply_officers' => $this->dropdown->supply_officers(),
                        'suppliers' => $this->dropdown->suppliers(),
                        'delivery_places' => $this->dropdown->dropdowns('Place of Delivery'),
                        'roles' => $this->dropdown->roles(),
                    ],
                    'tab' => $request->tab,
                    'procurement' => $procurement,
                    'logs' => $logs,
                    'option' => $request->option,
                ]);
            break;

             case 'view_notice_of_awards':
                $bac_resolution = ProcurementBac::findOrFail($request->bac_reso_id);
                return inertia('Modules/FAIMS/Procurement/View', [
                      'dropdowns' => [
                        'divisions' => $this->dropdown->dropdowns('Division'),
                        'fund_clusters' => $this->dropdown->dropdowns('Fund Cluster'),
                        'procurement_codes' => $this->dropdown->procurement_codes(),
                        'unit_types' => $this->dropdown->unit_types(),
                        'requesters' => $this->dropdown->requesters(),
                        'approvers' => $this->dropdown->approvers(),   
                        'supply_officers' => $this->dropdown->supply_officers(), 
                        'suppliers' => $this->dropdown->suppliers(),   
                        'delivery_places' => $this->dropdown->dropdowns('Place of Delivery'), 
                    ],
                    'procurement' => $procurement,
                    'bac_resolution' => $bac_resolution,
                    'option' => $request->option,
                ]); 
            break;
            case 'edit':
            case 'review':
            case 'approve':
                return inertia('Modules/FAIMS/Procurement/CreatePage', [
                    'dropdowns' => [
                        'divisions' => $this->dropdown->dropdowns('Division'),
                        'fund_clusters' => $this->dropdown->dropdowns('Fund Cluster'),
                        'procurement_codes' => $this->dropdown->procurement_codes(),
                        'unit_types' => $this->dropdown->unit_types(),
                        'requesters' => $this->dropdown->requesters(),
                        'approvers' => $this->dropdown->approvers(),     
                        
                    ],
                    'procurement' => $procurement,
                    'option' => $request->option,
                ]); 
            break;
            
            case 'quotations':
                return inertia('Modules/FAIMS/Procurement/Quotations/Index', [
                     'dropdowns' => [
                        'supply_officers' => $this->dropdown->supply_officers(), 
                        'suppliers' => $this->dropdown->suppliers(), 
                    ],
                    'procurement' => $procurement,
                    'option' => $request->option,
                ]); 
            break;

            case 'bids':
                return inertia('Modules/FAIMS/Procurement/Bids/Index', [
                     'dropdowns' => [
                        'suppliers' => $this->dropdown->suppliers(), 
                    ],
                    'procurement' => $procurement,
                    'option' => $request->option,
                ]); 
            break;

            case 'bac_resolutions':
                return inertia('Modules/FAIMS/Procurement/BACResolution/Index', [
                    'procurement' => $procurement,
                    'option' => $request->option,
                ]); 
            break;
 
            case 'notice_of_awards':
                $bac_resolution = ProcurementBac::findOrFail($request->bac_reso_id);
                return inertia('Modules/FAIMS/Procurement/NOA/Index', [
                    'procurement' => $procurement,
                    'bac_resolution' => $bac_resolution,
                    'option' => $request->option,
                ]); 
            break;

            case 'purchase_order':
                $noa = ProcurementBacNoa::with('purchase_order', 'procurement_quotation.supplier.address' , 'items')->findOrFail($request->noa_id);
                return inertia('Modules/FAIMS/Procurement/PurchaseOrder/Index', [
                    'dropdowns' => [
                        'delivery_places' => $this->dropdown->dropdowns('Place of Delivery'), 
                    ],
                    
                    'procurement' => $procurement,
                    'noa' => $noa,
                    'option' => $request->option,
                ]); 
            break;

            

        }
    }



}
