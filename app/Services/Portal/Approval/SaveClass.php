<?php

namespace App\Services\Portal\Approval;

use Hashids\Hashids;
use App\Models\Request;
use App\Models\RequestReport;
use App\Models\RequestOvertime;
use App\Models\RequestTravelCode;
use App\Models\OrgSignatory;
use App\Models\OrgSignatorySchedule;
use App\Models\RequestSignatory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SaveClass
{
    public function status($request)
    {
        try {
            $hashids = new Hashids('krad', 10);
            $signatoryId = $hashids->decode($request->id)[0] ?? null;
            $requestId   = $hashids->decode($request->request_id)[0] ?? null;

            if (!$requestId) {
                return [
                    'data' => null,
                    'message' => 'Invalid request ID',
                    'info' => 'The request ID provided could not be verified. Please try again or contact your administrator.',
                ];
            }

            $data = Request::find($requestId);

            if (!$data) {
                return [
                    'data' => null,
                    'message' => 'Request not found',
                    'info' => 'The request record could not be found. It may have been deleted or is no longer accessible.',
                ];
            }

            $user = OrgSignatorySchedule::where('user_id', auth()->id())
                ->where('is_ongoing', 1)
                ->first();

            $division = OrgSignatory::where(function ($q) {
                    $q->where('user_id', auth()->id())
                    ->orWhere('oic_id', auth()->id());
                })
                ->where('is_active', 1)
                ->first()?->designationable?->assigned;

            if (!$division) {
                return [
                    'data' => $data,
                    'message' => 'No active division found',
                    'info' => 'The system could not find your active division assignment. Please check your signatory status or contact HR/Admin.',
                ];
            }

            $query = RequestSignatory::where('id', $signatoryId)->where('request_id', $data->id);
            ($request->status_id != 26) ? $query->where('division_id', $division->id) : '';
            $signatory = $query->first();

            if (!$signatory) {
                $query = RequestSignatory::where('id', $signatoryId)->where('request_id', $data->id);
                $query->where('division_id', 1);
                $signatory = $query->first();

                if(!$signatory){
                    return [
                        'data' => $data,
                        'message' => 'Signatory record not found',
                        'info' => 'Unable to locate the corresponding signatory record for this request. Please refresh or check your permissions.',
                        'status' => false
                    ];
                }
            }

            switch ($request->status_id) {
                case 25:
                    $signatory->update([
                        'recommended_id'   => $user?->id,
                        'recommended_date' => now(),
                        'recommended_by'   => $this->image($request, $signatoryId),
                        'status_id'        => $request->status_id,
                    ]);
                break;
                case 26:
                    $signatory->update([
                        'approved_id'   => $user?->id,
                        'approved_date' => now(),
                        'approved_by'   => $this->image($request, $signatoryId),
                        'status_id'     => $request->status_id,
                        'is_completed'  => 1,
                    ]);

                    if ($data->type_id == 165) {
                        $this->overtime($data->id);
                    }
                break;
                case 30: 
                    $signatory->update([
                        'is_disapproved' => 1,
                        'status_id'      => $request->status_id,
                    ]);

                    if ($data->type_id == 158) {
                        $this->leave($data->id);
                    }
                break;
            }

            $signatory->statusable()->create([
                'user_id'   => auth()->id(),
                'status_id' => $request->status_id,
            ]);

            return [
                'data' => $data,
                'message' => 'Request Status Updated',
                'info' => 'The status of this request has been successfully updated. Please check your notifications for the latest details and next steps.',
            ];

        } catch (\Exception $e) {
            return [
                'data' => null,
                'message' => 'Update Failed',
                'info' => 'An error occurred while updating the request status. Please try again later or contact your administrator. (' . $e->getMessage() . ')',
            ];
        }
    }


    // public function status($request){
    //     $hashids = new Hashids('krad',10);
    //     $id = $hashids->decode($request->id);
    //     $request_id = $hashids->decode($request->request_id);

    //     $data = Request::find($request_id[0]);
    //     if($request->type != 'Travel Order'){
    //         $data->status_id = $request->status_id;

    //          $data->statuses()->create([
    //             'user_id' => \Auth::user()->id,
    //             'status_id' => $request->status_id
    //         ]);
    //     }
    //     if($data->save()){
    //         $user = OrgSignatorySchedule::where('user_id', \Auth::user()->id)->where('is_ongoing',1)->first();
    //         $division = OrgSignatory::where('user_id',\Auth::user()->id)->orWhere('oic_id',\Auth::user()->id)->where('is_active',1)->first(); 
    //         $division = $division?->designationable?->assigned;
        
    //         if($request->status_id == 25){
               
    //             $signatory = RequestSignatory::where('id',$id[0])->where('division_id',$division->id)->where('request_id',$data->id)->where('is_approval_only',0)->first();
        
    //             $signatory->recommended_id = $user->id;
    //             $signatory->status_id = $request->status_id;
    //             $signatory->recommended_date = now();
    //             $signatory->recommended_by = $this->image($request,$id[0]);
    //             $signatory->save();
    //             // $this->updateSignatory($data->id,'recommended',$user->is_designated,$division->others);
    //         }else if($request->status_id == 26){
    //             $signatory = RequestSignatory::where('id',$id[0])->where('request_id',$data->id)->update([
    //                 'approved_id' => $user->id,
    //                 'approved_date' => now(),
    //                 'status_id' => $request->status_id,
    //                 'approved_by' => $this->image($request,$id[0]),
    //                 'is_completed' => 1
    //             ]);
    //             if($signatory){
                   
    //                 // $this->updateSignatory($data->id,'approved',$user->is_designated);

    //                 if($data->type_id == 165){
    //                     $this->overtime($data->id);
    //                 }
    //             }
    //         }else if($request->status_id == 30){
    //             $signatory = RequestSignatory::where('id',$id[0])->where('request_id',$data->id)->update([
    //                 'is_disapproved' => 1,
    //                 'status_id' => $request->status_id
    //             ]);
    //             if($signatory){
    //                 if($data->type_id == 158){
    //                     $this->leave($data->id);
    //                 }
    //             }
    //         }
    //     }

    //     return [
    //         'data' => $data,
    //         'message' => 'Request Status Updated',
    //         'info' => "The status of this request has been successfully updated. Please check your notifications for the latest details and next steps."
    //     ];
    // }

    public function overtime($id){
        $data = new RequestOvertime;
        $data->code = $this->generateCode();
        $data->request_id = $id;
        $data->status_id = 31;
        $data->save();
    }

    private function image($request,$id){
        $image = $request->input('photo'); // base64 string

        // Validate format
        if (!preg_match('/^data:image\/(\w+);base64,/', $image, $matches)) {
            return response()->json(['error' => 'Invalid image format.'], 422);
        }

        $type = strtolower($matches[1]); // png, jpg, jpeg, gif
        if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
            return response()->json(['error' => 'Invalid image type.'], 422);
        }

        // Remove header and decode
        $image = substr($image, strpos($image, ',') + 1);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        if ($imageData === false) {
            return response()->json(['error' => 'Base64 decode failed.'], 422);
        }

        // Save to storage/app/public/images/attendance
        $filename = $id.Str::random(10) . '.' . $type;
        $path = 'images/signatory/' . $filename;
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }

    private function generateCode()
    {
        return \DB::transaction(function () {
            $latest = RequestOvertime::lockForUpdate()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->orderByDesc('id')
                ->first();

            $count = $latest
                ? (int) substr($latest->code, -4) + 1
                : 1;

            $code = now()->format('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            return $code;
        });
    }

    private function updateSignatory($requestId,$type,$is_designated,$division = null)
    {
        $report = RequestReport::where('request_id', $requestId)->first();
        $information = json_decode($report->information, true);
    
        if($type == 'recommended'){
            $role = ($is_designated) ? 'Assistant Regional Director ('.$division.')' : 'OIC - '.'Assistant Regional Director ('.$division.')';
        }else{
            $role = ($is_designated) ? '' : 'OIC - '.'Regional Director';
        }
        $information[$type] = [
            'name' => \Auth::user()->profile->fullname,
            'signature' => \Auth::user()->profile->signature,
            'role' => $role
        ];
        $report->information = json_encode($information);
        $report->save();

        return true;
    }
}
