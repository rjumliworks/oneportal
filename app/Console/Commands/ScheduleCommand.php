<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\UserRole;
use App\Jobs\SmsJob;
use Illuminate\Console\Command;

class ScheduleCommand extends Command
{
    protected $signature = 'notify:schedule';

    protected $description = 'Notify users via SMS about events';

    public function handle()
    {
        $today = Carbon::today();
        $schedules = Schedule::with('event','user')
        ->whereHas('event', function ($query) {
            $query->where('type','official');
        })
        ->whereDate('start',$today)->get();

        $counter = 1;
        $message = "Good day. This is to inform you that the following activities are scheduled for today, ".$today->format('F j, Y')."\n\n";
        foreach ($schedules as $schedule) {
            $message .= "$counter. {$schedule->title} ({$schedule->event->name})\n";
            $counter++;
        }
        $message .= "\nThank you!";

        $users = UserRole::with('user.profile')->whereIn('role_id',[2,3,4])->get();
        if(count($schedules) > 0){
            foreach($users as $user){
                dispatch(new SmsJob($user->user->profile->mobile, $message));
            }   
        }
        
    }
}
