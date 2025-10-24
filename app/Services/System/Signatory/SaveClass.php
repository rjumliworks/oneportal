<?php

namespace App\Services\System\Signatory;

use Carbon\Carbon;
use App\Models\Signatory;
use App\Models\SignatorySchedule;
use App\Models\OrganizationalChart;
use Illuminate\Support\Facades\DB;

class SaveClass
{
    public function signatory($request)
    {
        if (now()->greaterThanOrEqualTo($request->start_at)) {
            $data = SignatorySchedule::create($request->all());
            $signatory = Signatory::find($request->signatory_id);
            $signatory->update([
                'oic_id' => $request->user_id,
                'is_oic' => 1,
            ]);
            $data->update(['is_ongoing' => 1]);
        } else {
            $data = SignatorySchedule::create(
                array_merge($request->all(), ['is_ongoing' => 0])
            );
        }

        return [
            'data' => $data,
            'message' => 'Employee created successfully',
            'info' => 'You can now manage this employee’s details in the system',
        ];
    }

    public function designate($request)
    {
        if(now()->greaterThanOrEqualTo($request->start_at)){
            $data = SignatorySchedule::create($request->all());
            $chart = OrganizationalChart::find($request->signatory_id);
            $chart->update([
                'oic_id' => $request->user_id,
                'is_oic' => 1,
            ]);
            if($chart){
                $signatory = Signatory::find($request->signatory_id);
                $signatory->update([
                    'oic_id' => $request->user_id,
                    'is_oic' => 1,
                ]);
            }
            $data->update(['is_ongoing' => 1]);
        }else{
            $data = SignatorySchedule::create(
                array_merge($request->all(), ['is_ongoing' => 0])
            );
        }

        return [
            'data' => $data,
            'message' => 'Employee created successfully',
            'info' => 'You can now manage this employee’s details in the system',
        ];
    }
}
