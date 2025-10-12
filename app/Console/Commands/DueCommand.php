<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Tsr;
use App\Jobs\SmsJob;
use App\Models\UserRole;
use App\Models\ListLaboratory;
use Illuminate\Console\Command;

class DueCommand extends Command
{
    protected $signature = 'notify:due-tsrs';
    protected $description = 'Notify analysts via SMS about TSRs due today at 8:00 AM';
    
    public function handle()
    {
        $today = Carbon::today();
        $laboratories = ListLaboratory::where('is_active',1)->get();

        foreach($laboratories as $laboratory){
            $tsrs = Tsr::where('laboratory_type',$laboratory->id)->whereDate('due_at', $today)->get();
            $counter = 1;
            $message = "Good day. This is to inform you that the Reports for the following Technical Service Request for ".$laboratory->name." are due today, ".$today->format('F j, Y')."\n\n";
            foreach ($tsrs as $tsr) {
                $message .= "$counter. TSR Code : {$tsr->code}\n";
                $counter++;
            }
            $message .= "\nKindly ensure that the above mentioned reports are completed before 4:00 PM today.\n\nThank you!";

            $users = UserRole::with('user.profile')->where('laboratory_type',$laboratory->id)->whereIn('role_id',[4,2])->get();
            if(count($tsrs) > 0){
                foreach($users as $user){
                    dispatch(new SmsJob($user->user->profile->mobile, $message));
                }   
            }
        }
    }
}
