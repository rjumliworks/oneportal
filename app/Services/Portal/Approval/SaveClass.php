<?php

namespace App\Services\Portal\Approval;

use Hashids\Hashids;
use App\Models\Request;
use App\Models\RequestOvertime;
use App\Models\Signatory;
use App\Models\RequestSignatory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SaveClass
{
    public function status($request){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($request->id);

        $data = Request::find($id[0]);
        $data->status_id = $request->status_id;
        if($data->save()){
            if($request->status_id == 25){
                $division = Signatory::where('user_id',\Auth::user()->id)->orWhere('oic_id',\Auth::user()->id)->where('is_active',1)->first(); 
                $division = $division?->designationable?->assigned_id;
                $signatory = RequestSignatory::where('division_id',$division)->where('request_id',$data->id)->where('is_approval_only',0)->first();
        
                $signatory->recommended_id = \Auth::user()->id;
                $signatory->recommended_date = now();
                $signatory->recommended_by = $this->image($request,$id[0]);
                $signatory->save();
            }else if($request->status_id == 26){
                $signatory = RequestSignatory::where('request_id',$data->id)->update([
                    'approved_id' => \Auth::user()->id,
                    'approved_date' => now(),
                    'approved_by' => $this->image($request,$id[0]),
                    'is_completed' => 1
                ]);
                if($signatory){
                    if($data->type_id == 165){
                        $this->overtime($data->id);
                    }
                }
            }else if($request->status_id == 30){
                $signatory = RequestSignatory::where('request_id',$data->id)->update([
                    'is_disapproved' => 1
                ]);
                if($signatory){
                    if($data->type_id == 158){
                        $this->leave($data->id);
                    }
                }
            }
            $data->statuses()->create([
                'user_id' => \Auth::user()->id,
                'status_id' => $request->status_id
            ]);
        }

        return [
            'data' => $data,
            'message' => 'Request Status Updated',
            'info' => "The status of this request has been successfully updated. Please check your notifications for the latest details and next steps."
        ];
    }

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
}
