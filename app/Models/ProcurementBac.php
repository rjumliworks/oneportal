<?php

namespace App\Models;

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
       

        //check ih items with status "Avaliable for Re-award"
        $hasAvailableReAwardItems = $this->procurement->quotations->flatMap->items
            ->contains(fn($item) => $item->status_id == 40);    

        $availableQuotationItems = $this->procurement->quotations
            ->flatMap->items
            ->filter(fn($item) => $item->status_id == 40 || $item->status_id == 43 );

 
        // Some NOAs served → Partially Awarded
        if ($noas->contains(fn($noa) => $noa->status->name == 'Served to Supplier')) {

            if ($noas->contains(fn($noa) => $noa->status->name === 'Conformed')) {
                return 58; // Partially NOA Conformed
            }
            // Check if all NOAs are served
            if ($noas->every(fn($noa) => $noa->status->name === 'Served to Supplier')) {
                return 48; // NOA Served to Supplier
            }
  
            // Any NOAs not confirmed / Not Conformed → Re-Award or Re-Bid
            if ($noas->contains(fn($n) => $n->status->name === 'Not Conformed') ) {
                // STEP 1: Find the first Not Conformed NOA
                foreach ($noas as $noa) {
                    if ($noa->status->name === 'Not Conformed') {
                      
                         // Check if ANY item in ANY NOA is Available for Re-Award (40)
                        $hasReAwardItems = $noa->procurement_quotation->items
                                            ->contains(fn($item) => $item->status_id == 40);
        
                        if ($hasReAwardItems) {
                            return 59; // Re-Award                  
                        }
                        if(count($availableQuotationItems) > 0){
                            // STEP 2: Update item statuses only after checking all NOAs
                            foreach ($availableQuotationItems as $item) {
                                if ($item->status_id == 40) {
                                    // Available for Re-Award → Awarded
                                    $item->update(['status_id' => 43]);
                                }

                                else if ($item->status_id == 43) {
                                    // Awarded → Not Conformed
                                    $item->update(['status_id' => 68]);
                                }
                            }
                        }
                        return 60; // rebid

                    }
                
                }

                return 60; // Rebid
            }
         
            return 57; // Partially Awarded
        }

         // All NOAs confirmed → NOA Confirmed
        if ($noas->contains(fn($noa) => $noa->status->name == 'Not Conformed')) {
       
            if(count($availableQuotationItems) > 0){
                // STEP 1: Find the first Not Conformed NOA
                foreach ($availableQuotationItems as $item) {

                    if ($item->status_id == 40) {
                        // Available for Re-Award → Awarded
                        $item->update(['status_id' => 43]);
                    }

                    else if ($item->status_id == 43) {
                        // Awarded → Not Conformed
                        $item->update(['status_id' => 68]);
                    }
                }

            }
            
            if ($hasAvailableReAwardItems) {
                return 59; // Re-Award 
            }

            return 60; // Rebid
        }

        // All NOAs confirmed → NOA Confirmed
        if ($noas->every(fn($noa) => $noa->status->name == 'Conformed')) {
            return 56; // NOA Conformed
        }

        // Some NOAs confirmed → Partially NOA Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'Conformed')) {
            // Some NOAs PO Pending → PO Partially Pending
            if ($noas->contains(fn($noa) => $noa->status->name === 'PO Pending')) {
                // All NOAs confirmed → NOA Confirmed
                if ($noas->every(fn($noa) => $noa->status->name == 'PO Pending')) {
                    return 62; // PO Pending
                }
                return 63; // Partially PO Pending
            }
            return 58; // Partially NOA Conformed
        }


        // Some NOAs PO Pending → PO Partially Pending
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Pending')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Pending')) {
                return 62; // PO Pending
            }
            else if ($noas->contains(fn($noa) => $noa->status->name === 'PO Issued')) {
                // Sme NOAs has PO Served to Supplier → PO Partially Served
                if ($noas->every(fn($noa) => $noa->status->name == 'PO Issued')) {
                    return 49; // PO Issued
                }
                return 64; // PO Partially Issued
            }
            return 63; // Partially PO Pending
        }

        // Some NOAs PO Served To Supplier → PO Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Issued')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Issued')) {
                return 49; // PO Issued
            }
            else  if ($noas->contains(fn($noa) => $noa->status->name === 'PO Conformed/NTP Created/Awaiting for Delivery')) {
                return 65; // PO Partially Conformed
            }
            return 64; // Partially PO Issued
        }

        // Some NOAs PO Served To Supplier → PO Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Conformed/NTP Created/Awaiting for Delivery')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Conformed/NTP Created/Awaiting for Delivery')) {
                return 51; // PO Conformed
            }
            // Some NOAs Delivered/For Inspection 
            if ($noas->contains(fn($noa) => $noa->status->name === 'Delivered/For Inspection')) {
                // All NOAs confirmed → NOA Confirmed
                if ($noas->every(fn($noa) => $noa->status->name == 'Delivered/For Inspection')) {
                    return 52; // Delivered/For Inspection
                }
                return 66; // Partially Delivered/For Inspection
            }
            return 65; // PO Partially Conformed
        }

        // Some NOAs Delivered/For Inspection  and Completed
        if ($noas->contains(fn($noa) => $noa->status->name === 'Delivered/For Inspection')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'Delivered/For Inspection')) {
                return 52; // Delivered/For Inspection
            }
            if ($noas->contains(fn($noa) => $noa->status->name == 'Completed')) {
                return 67; // Partially Completed/Awaiting for Inspection
            }
            return 66; // Partially Delivered/For Inspection
        }

           // Some NOAs Delivered/For Inspection 
        if ($noas->contains(fn($noa) => $noa->status->name === 'Completed')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'Completed')) {
                return 53; // Completed
            }
            return 67; // Partially Completed/Awaiting for Inspection
        }

      
         // Some NOAs has status "PO Not Conformed"
        if ($noas->contains(fn($noa) => $noa->status->name == 'PO Not Conformed')) {

            if(count($availableQuotationItems) > 0){
                // STEP 1: Find the first Not Conformed NOA
                foreach ($availableQuotationItems as $item) {
                    if ($item->status_id == 40) {
                        // Available for Re-Award → Awarded
                        $item->update(['status_id' => 43]);
                    }
                    else if ($item->status_id == 43) {
                        // Awarded → Not Conformed
                        $item->update(['status_id' => 61]);
                    }
                }
            }
          

            if ($hasAvailableReAwardItems) {
                return 59; // Re-Award 
            }
                        
            //dd('rebid');
            return 60; // Rebid
        }


          // All NOAs has status "PO Not Conformed"
        if ($noas->every(fn($noa) => $noa->status->name == 'PO Not Conformed')) {
       
            if(count($availableQuotationItems) > 0){
                // STEP 1: Find the first Not Conformed NOA
                foreach ($availableQuotationItems as $item) {
                    if ($item->status_id == 40) {
                        // Available for Re-Award → Awarded
                        $item->update(['status_id' => 43]);
                    }
                    else if ($item->status_id == 43) {
                        // Awarded → Not Conformed
                        $item->update(['status_id' => 61]);
                    }
                }
            }

            if ($hasAvailableReAwardItems) {
                return 59; // Re-Award 
            }

            return 60; // Rebid
        }

        return $current_status;

    }

    public function overall_substatus($current_status)
    {
        $noas = $this->notice_of_awards;


        //check ih items with status "Avaliable for Re-award"
        $hasAvailableReAwardItems = $this->procurement->quotations->flatMap->items
            ->contains(fn($item) => $item->status_id == 40);    
        
        $availableQuotationItems = $this->procurement->quotations
            ->flatMap->items
            ->filter(fn($item) => $item->status_id == 40 || $item->status_id == 43 );


        // Some NOAs served → Partially Awarded
        if ($noas->contains(fn($noa) => $noa->status->name == 'Served to Supplier')) {

            if ($noas->contains(fn($noa) => $noa->status->name === 'Conformed')) {
                return 58; // Partially NOA Conformed
            }
            // Check if all NOAs are served
            if ($noas->every(fn($noa) => $noa->status->name === 'Served to Supplier')) {
                return 48; // NOA Served to Supplier
            }
  
            //  Any NOAs not confirmed / Not Conformed → Re-Award or Re-Bid
            if ($noas->contains(fn($n) => $n->status->name === 'Not Conformed') ) {
                // STEP 1: Find the first Not Conformed NOA
                    if ($noa->status->name === 'Not Conformed') {
                        // STEP 1: Find the first Not Conformed NOA
                        foreach ($availableQuotationItems as $item) {
                            if ($item->status_id == 40) {
                                // Available for Re-Award → Awarded
                                $item->update(['status_id' => 43]);
                            }
                            if ($item->status_id == 43) {
                                // Awarded → Not Conformed
                                $item->update(['status_id' => 68]);
                            }
                        }
                    
                        if ($hasReAwardItems) {
                            return 59; // Re-Award 
                                
                        }
                        
                        return 60; // rebid

                    }
                return 60; // Rebid
            }

            return 57; // Partially Awarded
        }

         // All NOAs confirmed → NOA Confirmed
        if ($noas->contains(fn($noa) => $noa->status->name == 'Not Conformed')) {

             // STEP 1: Find the first Not Conformed NOA
            foreach ($availableQuotationItems as $item) {
                if ($item->status_id == 40) {
                    // Available for Re-Award → Awarded
                    $item->update(['status_id' => 43]);
                }
                if ($item->status_id == 43) {
                    // Awarded → Not Conformed
                    $item->update(['status_id' => 68]);
                }
            }

            if ($hasAvailableReAwardItems) {
                return 59; // Re-Award 
            }
                        
            return 60; // Rebid
        }

        // All NOAs confirmed → NOA Confirmed
        if ($noas->every(fn($noa) => $noa->status->name == 'Conformed')) {
            return 56; // NOA Conformed
        }

        // Some NOAs confirmed → Partially NOA Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'Conformed')) {
            // Some NOAs PO Pending → PO Partially Pending
            if ($noas->contains(fn($noa) => $noa->status->name === 'PO Pending')) {
                // All NOAs confirmed → NOA Confirmed
                if ($noas->every(fn($noa) => $noa->status->name == 'PO Pending')) {
                    return 62; // PO Pending
                }
                return 63; // Partially PO Pending
            }
            return 58; // Partially NOA Conformed
        }


        // Some NOAs PO Pending → PO Partially Pending
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Pending')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Pending')) {
                return 62; // PO Pending
            }
            else if ($noas->contains(fn($noa) => $noa->status->name === 'PO Issued')) {
                // Sme NOAs has PO Served to Supplier → PO Partially Served
                if ($noas->every(fn($noa) => $noa->status->name == 'PO Issued')) {
                    return 49; // PO Issued
                }
                return 64; // PO Partially Issued
            }
            return 63; // Partially PO Pending
        }



        // Some NOAs PO Served To Supplier → PO Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Issued')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Issued')) {
                return 49; // PO Issued
            }
            else  if ($noas->contains(fn($noa) => $noa->status->name === 'PO Conformed/NTP Created/Awaiting for Delivery')) {
                return 65; // PO Partially Conformed
            }
            return 64; // Partially PO Issued
        }

        // Some NOAs PO Served To Supplier → PO Conformed
        if ($noas->contains(fn($noa) => $noa->status->name === 'PO Conformed/NTP Created/Awaiting for Delivery')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'PO Conformed/NTP Created/Awaiting for Delivery')) {
                return 51; // PO Conformed
            }
            // Some NOAs Delivered/For Inspection 
            if ($noas->contains(fn($noa) => $noa->status->name === 'Delivered/For Inspection')) {
                // All NOAs confirmed → NOA Confirmed
                if ($noas->every(fn($noa) => $noa->status->name == 'Delivered/For Inspection')) {
                    return 52; // Delivered/For Inspection
                }
                return 66; // Partially Delivered/For Inspection
            }
            return 65; // PO Partially Conformed
        }

        // Some NOAs Delivered/For Inspection  and Completed
        if ($noas->contains(fn($noa) => $noa->status->name === 'Delivered/For Inspection')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'Delivered/For Inspection')) {
                return 52; // Delivered/For Inspection
            }
            if ($noas->contains(fn($noa) => $noa->status->name == 'Completed')) {
                return 67; // Partially Completed/Awaiting for Inspection
            }
            return 66; // Partially Delivered/For Inspection
        }

           // Some NOAs Delivered/For Inspection 
        if ($noas->contains(fn($noa) => $noa->status->name === 'Completed')) {
             // All NOAs confirmed → NOA Confirmed
            if ($noas->every(fn($noa) => $noa->status->name == 'Completed')) {
                return 53; // Completed
            }
            return 67; // Partially Completed/Awaiting for Inspection
        }


        // Some NOAs has status "PO Not Conformed"
        if ($noas->contains(fn($noa) => $noa->status->name == 'PO Not Conformed')) {

            // STEP 1: Find the first Not Conformed NOA
            foreach ($availableQuotationItems as $item) {
                if ($item->status_id == 40) {
                    // Available for Re-Award → Awarded
                    $item->update(['status_id' => 43]);
                }
                else if ($item->status_id == 43) {
                    // Awarded → Not Conformed
                    $item->update(['status_id' => 61]);
                }
            }

            if ($hasAvailableReAwardItems) {
                return 59; // Re-Award 
            }
                        
            //dd('rebid');
            return 60; // Rebid
        }

          // All NOAs has status "PO Not Conformed"
        if ($noas->every(fn($noa) => $noa->status->name == 'PO Not Conformed')) {
       
            // STEP 1: Find the first Not Conformed NOA
            foreach ($availableQuotationItems as $item) {
                if ($item->status_id == 40) {
                    // Available for Re-Award → Awarded
                    $item->update(['status_id' => 43]);
                }
                else if ($item->status_id == 43) {
                    // Awarded → Not Conformed
                    $item->update(['status_id' => 61]);
                }
            }

            if ($hasAvailableReAwardItems) {
                return 59; // Re-Award 
            }

            return 60; // Rebid
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
