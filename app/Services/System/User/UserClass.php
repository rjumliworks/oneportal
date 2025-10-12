<?php

namespace App\Services\System\User;

use App\Models\User;
use App\Models\UserRole;
use App\Http\Resources\System\UserResource;
use App\Http\Resources\System\RoleResource;

class UserClass
{
    public function list($request){
        $data = User::with('profile:user_id,firstname,middlename,lastname,suffix_id,avatar,mobile')
        ->with('myroles:role_id,id,user_id,added_by,removed_by,removed_at,created_at,is_active','myroles.role:id,name','myroles.added:id','myroles.added.profile:user_id,firstname,middlename,lastname,suffix_id','myroles.removed:id','myroles.removed.profile:user_id,firstname,middlename,lastname,suffix_id')
        ->paginate($request->count);
        return UserResource::collection($data);
    }

    public function status($request){
        $data = User::with('profile:user_id,firstname,middlename,lastname,suffix_id,avatar,mobile')
        ->with('myroles:role_id,id,user_id','myroles.role:id,name')
        ->where('id',$request->user_id)->first();
        $data->is_active = $request->is_active;
        $data->save();

        return [
            'data' => new UserResource($data),
            'message' => 'User update was successful!', 
            'info' => "You've successfully updated the selected user."
        ];
    }

    public function credential($request){
        $data = User::with('profile')->find($request->user_id);
        $data->email = $request->email;
        if ($request->set) {
            $data->password = bcrypt($request->password);
            $data->must_change = 1;
        }
        if($data->save() && $data->profile){
            $data->profile->mobile = $request->mobile;
            $data->profile->save();
        }
        $data = User::with('profile:user_id,firstname,middlename,lastname,suffix_id,avatar,mobile')
        ->with('myroles:role_id,id,user_id','myroles.role:id,name')
        ->where('id',$request->user_id)->first();
        return [
            'data' => new UserResource($data),
            'message' => 'User update was successful!', 
            'info' => "You've successfully updated the selected user."
        ];
    }

    public function role($request){
        if($request->type == 'remove'){
            $data = UserRole::find($request->id);
            $data->removed_by = \Auth::user()->id;
            $data->removed_at = now();
            $data->is_active = 0;
            $data->save();
        }

        $data = UserRole::with('role:id,name','added:id','added.profile:user_id,firstname,middlename,lastname,suffix_id','removed:id','removed.profile:user_id,firstname,middlename,lastname,suffix_id')->where('id',$request->id)->first();
        return [
            'data' => new RoleResource($data),
            'message' => 'User role remove was successful!', 
            'info' => "You've successfully updated the selected user."
        ];
    }
}
