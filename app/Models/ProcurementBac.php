<?php

namespace App\Models;

use App\Models\ListStatus;
use Illuminate\Database\Eloquent\Model;

class ProcurementBac extends Model
{
    protected $fillable = [
        'procurement_id',
        'code',
        'type',
        'body',
        'created_by_id',
        'approved_by_id',
        'status_id',
    ];

    public function procurement()
    {
        return $this->belongsTo('App\Models\Procurement', 'procurement_id')->with('quotations.items');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\User', 'created_by_id')->with('profile');
    }

    
    public function approved_by()
    {
        return $this->belongsTo('App\Models\User', 'approved_by_id')->with('profile');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\ListStatus', 'status_id');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\RequestComment', 'commentable');
    }


    public static function generateBACResolutionNumber($date = null)
    {
        if ($date) {
            $year = date("y", strtotime($date));  // 'y' gives the last two digits of the year
            $month = date("m", strtotime($date));
        } else {
            $year = date("y", strtotime("now"));  // 'y' gives the last two digits of the year
            $month = date("m", strtotime("now"));
        }
    
        $count = self::whereYear('created_at', date("Y", strtotime($date ?? "now")))
                     ->whereMonth('created_at', $month)
                     ->count() + 1;
    
        return 'BAC-' .$year . '-' . $month . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

     public function notice_of_awards()
    {
        return $this->hasMany('App\Models\ProcurementBacNoa', 'procurement_bac_id')->with('status' , 'items' , 'procurement_quotation');
    }

    public function overall_status($current_status)
    {
        $noas = $this->notice_of_awards;
       

        //check ih items with status "Available for Re-award"
        $hasAvailableReAwardItems = $this->procurement->quotations->flatMap->items
            ->contains(fn($item) => $item->status_id == ListStatus::getID('Available for Re-award','Procurement'));    

        $availableQuotationItems = $this->procurement->quotations
            ->flatMap->items
            ->filter(fn($item) => $item->status_id == ListStatus::getID('Available for Re-award','Procurement') || $item->status_id == ListStatus::getID('Awarded','Procurement') );

 
        // Some NOAs served → Partially Awarded
        if ($noas->contains(fn($noa) => $noa->status->name == 'Served to Supplier')) {

            if ($noas->contains(fn($noa) => $noa->status->name === 'Conformed')) {
                return ListStatus::getID('Partially NOA Conformed','Procurement'); // Partially NOA Conformed
            }
            // Check if all NOAs are served
            if ($noas->every(fn($noa) => $noa->status->name === 'Served to Supplier')) {
                return ListStatus::getID('NOA Served to Supplier','Procurement'); // NOA Served to Supplier
            }
  
            // Any NOAs not confirmed / Not Conformed → Re-award or Re-Bid
            if ($noas->contains(fn($n) => $n->status->name === 'Not Conformed') ) {
                // STEP 1: Find the first Not Conformed NOA
                foreach ($noas as $noa) {
                    if ($noa->status->name === 'Not Conformed') {
                      
                         // Check if ANY item in ANY NOA is Available for Re-award (40)
                        $hasReAwardItems = $noa->procurement_quotation->items
                                            ->contains(fn($item) => $item->status_id == ListStatus::getID('Available for Re-award','Procurement'));
        
                        if ($hasReAwardItems) {
                            return ListStatus::getID('Re-award','Procurement'); // Re-award                  
                        }
                        if(count($availableQuotationItems) > 0){
                            // STEP 2: Update item statuses only after checking all NOAs
                            foreach ($availableQuotationItems as $item) {
                                if ($item->status_id == ListStatus::getID('Available for Re-award','Procurement')) {
                                    // Available for Re-award → Awarded
                                    $item->update(['status_id' => ListStatus::getID('Awarded','Procurement')]);
                                }

                                else if ($item->status_id == ListStatus::getID('Awarded','Procurement')) {
                                    // Awarded → Not Conformed
                                    $item->update(['status_id' => ListStatus::getID('Not Conformed','Procurement')]);
                                }
                            }
                        }
                        return ListStatus::getID('Rebid','Procurement'); // rebid

                    }
                
                }

                return ListStatus::getID('Rebid','Procurement'); // Rebid
            }
         
            return ListStatus::getID('Partially Awarded','Procurement'); // Partially Awarded
        }

         // All NOAs confirmed → NOA Confirmed
        if ($noas->contains(fn($noa) => $noa->status->name == 'Not Conformed')) {
       
            if(count($availableQuotationItems) > 0){
                // STEP 1: Find the first Not Conformed NOA
                foreach ($availableQuotationItems as $item) {

                    if ($item->status_id == ListStatus::getID('Available for Re-award','Procurement')) {
                        // Available for Re-award → Awarded
                        $item->update(['status_id' => ListStatus::getID('Awarded','Procurement')]);
                    }

                    else if ($item->status_id == ListStatus::getID('Awarded','Procurement')) {
                        // Awarded → Not Conformed
                        $item->update(['status_id' => ListStatus::getID('Not Conformed','Procurement')]);
                    }
                }

            }
            
            if ($hasAvailableReAwardItems) {
                return ListStatus::getID('Re-award','Procurement'); 
            }

            return ListStatus::getID('Rebid','Procurement');
        }

        // All NOAs confirmed → NOA Confirmed
        if ($noas->every(fn($noa) => $noa->status->name == 'Conformed')) {
            return ListStatus::getID('NOA Conformed','Procurement'); 
        }

        // Some NOAs confirmed → Partially NOA Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'Conformed')) {
            // Some NOAs PO Pending → PO Partially Pending
            if ($noas->contains(fn($noa) => $noa->status->name === 'PO Pending')) {
                // All NOAs confirmed → NOA Confirmed
                if ($noas->every(fn($noa) => $noa->status->name == 'PO Pending')) {
                    return ListStatus::getID('PO Pending','Procurement'); 
                }
                return ListStatus::getID('Partially PO Pending','Procurement'); 
            }
            return ListStatus::getID('Partially NOA Conformed','Procurement'); // Partially NOA Conformed
        }


        // Some NOAs PO Pending → PO Partially Pending
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Pending')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Pending')) {
                return ListStatus::getID('PO Pending','Procurement'); // PO Pending
            }
            else if ($noas->contains(fn($noa) => $noa->status->name === 'PO Issued')) {
                // Sme NOAs has PO Served to Supplier → PO Partially Served
                if ($noas->every(fn($noa) => $noa->status->name == 'PO Issued')) {
                    return ListStatus::getID('PO Issued','Procurement'); // PO Issued
                }
                return ListStatus::getID('PO Partially Issued','Procurement'); 
            }
            return ListStatus::getID('Partially PO Pending','Procurement'); 
        }

        // Some NOAs PO Served To Supplier → PO Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Issued')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Issued')) {
                return ListStatus::getID('PO Issued','Procurement'); 
            }
            else  if ($noas->contains(fn($noa) => $noa->status->name === 'PO Conformed')) {
                return ListStatus::getID('PO Partially Conformed','Procurement'); 
            }
            return ListStatus::getID('Partially PO Issued','Procurement'); 
        }

        // Some NOAs PO Served To Supplier → PO Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Conformed')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Conformed')) {
                return ListStatus::getID('PO Conformed','Procurement'); 
            }
            // Some NOAs Delivered/For Inspection 
            if ($noas->contains(fn($noa) => $noa->status->name === 'Delivered/For Inspection')) {
                // All NOAs confirmed → NOA Confirmed
                if ($noas->every(fn($noa) => $noa->status->name == 'Delivered/For Inspection')) {
                    return ListStatus::getID('Delivered/For Inspection','Procurement'); 
                }
                return ListStatus::getID('Partially Delivered/For Inspection','Procurement'); 
            }
            return ListStatus::getID('PO Partially Conformed','Procurement'); 
        }

        // Some NOAs Delivered/For Inspection  and Completed
        if ($noas->contains(fn($noa) => $noa->status->name === 'Delivered/For Inspection')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'Delivered/For Inspection')) {
                return ListStatus::getID('Delivered/For Inspection','Procurement'); // Delivered/For Inspection
            }
            if ($noas->contains(fn($noa) => $noa->status->name == 'Completed')) {
                return ListStatus::getID('Partially Completed','Procurement'); // Partially Completed/Awaiting for Inspection
            }
            return ListStatus::getID('Partially Delivered/For Inspection','Procurement'); // Partially Delivered/For Inspection
        }

           // Some NOAs Delivered/For Inspection 
        if ($noas->contains(fn($noa) => $noa->status->name === 'Completed')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'Completed')) {
                return ListStatus::getID('Completed','Procurement'); // Completed
            }
            return ListStatus::getID('Partially Completed','Procurement'); // Partially Completed
        }

      
         // Some NOAs has status "PO Not Conformed"
        if ($noas->contains(fn($noa) => $noa->status->name == 'PO Not Conformed')) {

            if(count($availableQuotationItems) > 0){
                // STEP 1: Find the first Not Conformed NOA
                foreach ($availableQuotationItems as $item) {
                    if ($item->status_id == ListStatus::getID('Available for Re-award','Procurement')) {
                        // Available for Re-award → Awarded
                        $item->update(['status_id' => ListStatus::getID('Awarded','Procurement')]);
                    }
                    else if ($item->status_id == ListStatus::getID('Awarded','Procurement')) {
                        // Awarded → Not Conformed
                        $item->update(['status_id' => ListStatus::getID('Not Conformed','Procurement')]);
                    }
                }
            }
          

            if ($hasAvailableReAwardItems) {
                return ListStatus::getID('Re-award','Procurement');  
            }
                        
            //dd('rebid');
            return ListStatus::getID('Rebid','Procurement'); // Rebid
        }


          // All NOAs has status "PO Not Conformed"
        if ($noas->every(fn($noa) => $noa->status->name == 'PO Not Conformed')) {
       
            if(count($availableQuotationItems) > 0){
                // STEP 1: Find the first Not Conformed NOA
                foreach ($availableQuotationItems as $item) {
                    if ($item->status_id == ListStatus::getID('Available for Re-award','Procurement')) {
                        // Available for Re-award → Awarded
                        $item->update(['status_id' => ListStatus::getID('Awarded','Procurement')]);
                    }
                    else if ($item->status_id == ListStatus::getID('Awarded','Procurement')) {
                        // Awarded → Not Conformed
                        $item->update(['status_id' => ListStatus::getID('Not Conformed','Procurement')]);
                    }
                }
            }

            if ($hasAvailableReAwardItems) {
                return ListStatus::getID('Re-award','Procurement'); // Re-award 
            }

            return ListStatus::getID('Rebid','Procurement'); // Rebid
        }

        return $current_status;

    }

    public function overall_substatus($current_status)
    {
        $noas = $this->notice_of_awards;


        //check ih items with status "Available for Re-award"
        $hasAvailableReAwardItems = $this->procurement->quotations->flatMap->items
            ->contains(fn($item) => $item->status_id == ListStatus::getID('Available for Re-award','Procurement'));    
        
        $availableQuotationItems = $this->procurement->quotations
            ->flatMap->items
            ->filter(fn($item) => $item->status_id == ListStatus::getID('Available for Re-award','Procurement') || $item->status_id == ListStatus::getID('Awarded','Procurement') );


        // Some NOAs served → Partially Awarded
        if ($noas->contains(fn($noa) => $noa->status->name == 'Served to Supplier')) {

            if ($noas->contains(fn($noa) => $noa->status->name === 'Conformed')) {
                return ListStatus::getID('Partially NOA Conformed','Procurement'); // Partially NOA Conformed
            }
            // Check if all NOAs are served
            if ($noas->every(fn($noa) => $noa->status->name === 'Served to Supplier')) {
                return ListStatus::getID('NOA Served to Supplier','Procurement'); // NOA Served to Supplier
            }
  
            //  Any NOAs not confirmed / Not Conformed → Re-Award or Re-Bid
            if ($noas->contains(fn($n) => $n->status->name === 'Not Conformed') ) {
                // STEP 1: Find the first Not Conformed NOA
                    if ($noa->status->name === 'Not Conformed') {
                        // STEP 1: Find the first Not Conformed NOA
                        foreach ($availableQuotationItems as $item) {
                            if ($item->status_id == ListStatus::getID('Available for Re-award','Procurement')) {
                                // Available for Re-award → Awarded
                                $item->update(['status_id' => ListStatus::getID('Awarded','Procurement')]);
                            }
                            if ($item->status_id == ListStatus::getID('Awarded','Procurement')) {
                                // Awarded → Not Conformed
                                $item->update(['status_id' => ListStatus::getID('Not Conformed','Procurement')]);
                            }
                        }
                    
                        if ($hasReAwardItems) {
                            return ListStatus::getID('Re-award','Procurement'); 
                                
                        }
                        
                        return ListStatus::getID('Rebid','Procurement');  

                    }
                return ListStatus::getID('Rebid','Procurement'); // Rebid
            }

            return ListStatus::getID('Partially Awarded','Procurement'); // Partially Awarded
        }

         // All NOAs confirmed → NOA Confirmed
        if ($noas->contains(fn($noa) => $noa->status->name == 'Not Conformed')) {

             // STEP 1: Find the first Not Conformed NOA
            foreach ($availableQuotationItems as $item) {
                if ($item->status_id == ListStatus::getID('Available for Re-award','Procurement')) {
                    // Available for Re-award → Awarded
                    $item->update(['status_id' => ListStatus::getID('Awarded','Procurement')]);
                }
                if ($item->status_id == ListStatus::getID('Available for Re-award','Procurement')) {
                    // Awarded → Not Conformed
                    $item->update(['status_id' =>ListStatus::getID('Not Conformed','Procurement')]);
                }
            }

            if ($hasAvailableReAwardItems) {
                return ListStatus::getID('Re-award','Procurement'); 
            }
                        
            return ListStatus::getID('Rebid','Procurement'); // Rebid
        }

        // All NOAs confirmed → NOA Confirmed
        if ($noas->every(fn($noa) => $noa->status->name == 'Conformed')) {
            return ListStatus::getID('NOA Conformed','Procurement'); // NOA Conformed
        }

        // Some NOAs confirmed → Partially NOA Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'Conformed')) {
            // Some NOAs PO Pending → PO Partially Pending
            if ($noas->contains(fn($noa) => $noa->status->name === 'PO Pending')) {
                // All NOAs confirmed → NOA Confirmed
                if ($noas->every(fn($noa) => $noa->status->name == 'PO Pending')) {
                    return ListStatus::getID('PO Pending','Procurement'); // PO Pending
                }
                return ListStatus::getID('Partially PO Pending','Procurement'); // Partially PO Pending
            }
            return ListStatus::getID('Partially NOA Conformed','Procurement'); // Partially NOA Conformed
        }


        // Some NOAs PO Pending → PO Partially Pending
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Pending')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Pending')) {
                return ListStatus::getID('PO Pending','Procurement'); // PO Pending
            }
            else if ($noas->contains(fn($noa) => $noa->status->name === 'PO Issued')) {
                // Sme NOAs has PO Served to Supplier → PO Partially Served
                if ($noas->every(fn($noa) => $noa->status->name == 'PO Issued')) {
                    return ListStatus::getID('PO Issued','Procurement'); // PO Issued
                }
                return ListStatus::getID('PO Partially Issued','Procurement'); // PO Partially Issued
            }
            return ListStatus::getID('Partially PO Pending','Procurement'); // Partially PO Pending
        }



        // Some NOAs PO Served To Supplier → PO Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Issued')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Issued')) {
                return ListStatus::getID('PO Issued','Procurement'); // PO Issued
            }
            else  if ($noas->contains(fn($noa) => $noa->status->name === 'PO Conformed')) {
                return ListStatus::getID('PO Partially Conformed','Procurement'); // PO Partially Conformed
            }
            return ListStatus::getID('Partially PO Issued','Procurement'); // Partially PO Issued
        }

        // Some NOAs PO Served To Supplier → PO Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Conformed')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Conformed')) {
                return ListStatus::getID('PO Conformed','Procurement'); // PO Conformed
            }
            // Some NOAs Delivered/For Inspection 
            if ($noas->contains(fn($noa) => $noa->status->name === 'Delivered/For Inspection')) {
                // All NOAs confirmed → NOA Confirmed
                if ($noas->every(fn($noa) => $noa->status->name == 'Delivered/For Inspection')) {
                    return ListStatus::getID('Delivered/For Inspection','Procurement'); // Delivered/For Inspection
                }
                return ListStatus::getID('Partially Delivered/For Inspection','Procurement'); // Partially Delivered/For Inspection
            }
            return ListStatus::getID('PO Partially Conformed','Procurement'); // PO Partially Conformed
        }

        // Some NOAs Delivered/For Inspection  and Completed
        if ($noas->contains(fn($noa) => $noa->status->name === 'Delivered/For Inspection')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'Delivered/For Inspection')) {
                return ListStatus::getID('Delivered/For Inspection','Procurement'); // Delivered/For Inspection
            }
            if ($noas->contains(fn($noa) => $noa->status->name == 'Completed')) {
                return ListStatus::getID('Partially Completed','Procurement'); // Partially Completed
            }
            return ListStatus::getID('Partially Delivered/For Inspection','Procurement'); // Partially Delivered/For Inspection
        }

           // Some NOAs Delivered/For Inspection 
        if ($noas->contains(fn($noa) => $noa->status->name === 'Completed')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'Completed')) {
                return ListStatus::getID('Completed','Procurement'); // Completed
            }
            return ListStatus::getID('Partially Completed','Procurement'); // Partially Completed
        }


        // Some NOAs has status "PO Not Conformed"
        if ($noas->contains(fn($noa) => $noa->status->name == 'PO Not Conformed')) {

            // STEP 1: Find the first Not Conformed NOA
            foreach ($availableQuotationItems as $item) {
                if ($item->status_id == ListStatus::getID('Available for Re-award','Procurement')) {
                    // Available for Re-award → Awarded
                    $item->update(['status_id' => ListStatus::getID('Awarded','Procurement')]);
                }
                else if ($item->status_id == ListStatus::getID('Awarded','Procurement')) {
                    // Awarded → Not Conformed
                    $item->update(['status_id' => ListStatus::getID('Not Conformed','Procurement')]);
                }
            }

            if ($hasAvailableReAwardItems) {
                return ListStatus::getID('Re-Award','Procurement'); // Re-Award 
            }
                        
            return ListStatus::getID('Rebid','Procurement'); // Rebid
        }

          // All NOAs has status "PO Not Conformed"
        if ($noas->every(fn($noa) => $noa->status->name == 'PO Not Conformed')) {
       
            // STEP 1: Find the first Not Conformed NOA
            foreach ($availableQuotationItems as $item) {
                if ($item->status_id == ListStatus::getID('Available for Re-award','Procurement')) {
                    // Available for Re-award → Awarded
                    $item->update(['status_id' => ListStatus::getID('Awarded','Procurement')]);
                }
                else if ($item->status_id == ListStatus::getID('Awarded','Procurement')) {
                    // Awarded → Not Conformed
                    $item->update(['status_id' => ListStatus::getID('Not Conformed','Procurement')]);
                }
            }

            if ($hasAvailableReAwardItems) {
                return ListStatus::getID('Re-award','Procurement'); // Re-award 
            }

            return ListStatus::getID('Rebid','Procurement'); // Rebid
        }

        return $current_status;

    }

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
        ->logOnly(['procurement_id','code','type','body','created_by_id','approved_by_id','status_id'])
        ->setDescriptionForEvent(fn(string $eventName) => "BAC Resolution {$eventName}")
        ->useLogName('BAC Resolution')
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
    }

}
