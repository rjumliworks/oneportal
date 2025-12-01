<?php

namespace App\Services\System\User;

use Hashids\Hashids;
use App\Jobs\UploaderJob;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserFolder;
use App\Models\UserFolderFile;
use Illuminate\Support\Carbon;
use App\Models\AuthenticationLog;
use Spatie\Activitylog\Models\Activity;
use App\Http\Resources\DefaultResource;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\AuthenticationResource;
use App\Http\Resources\System\User\UserResource;
use App\Http\Resources\System\User\RoleResource;
use App\Http\Resources\System\User\ViewResource;
use App\Http\Resources\System\User\FileResource;
use Illuminate\Support\Facades\Storage;
use Aws\Rekognition\RekognitionClient;

class UserClass
{
    public function view($code){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($code);

        $data = new ViewResource(
            User::query()
            ->with('profile:user_id,firstname,middlename,lastname,suffix_id,avatar,mobile')
            ->with('myroles:role_id,id,user_id,added_by,removed_by,removed_at,created_at,is_active','myroles.role:id,name','myroles.added:id','myroles.added.profile:user_id,firstname,middlename,lastname,suffix_id','myroles.removed:id','myroles.removed.profile:user_id,firstname,middlename,lastname,suffix_id')
            ->where('id',$id)->first()
        );
        return $data;
    }

    public function authentication($request){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($request->code);
        $data = AuthenticationLog::with('user.profile')->where('user_id',$id)->orderBy('created_at','DESC')->paginate($request->count);
        return AuthenticationResource::collection($data);
    }

    public function files($request)
    {
        $hashids = new Hashids('krad', 10);
        $id = $hashids->decode($request->code)[0];

        $data = UserFolderFile::whereHas('folder', function ($query) use ($id) {
            $query->where('name', 'Reference1')
                ->where('user_id', $id);
        })->get();

        // Add signed URL to each file
        $data->transform(function ($file) {
            $file->signed_url = Storage::disk('s3')->temporaryUrl(
                $file->path,            // must be relative key, e.g. "oneportal/69154a7b3fffd.png"
                now()->addMinutes(5)    // expiration
            );
            return $file; // important!
        });

        return DefaultResource::collection($data);
    }

    public function activity($request){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($request->code);
        $data = Activity::with('causer.profile')->where('causer_id',$id)->orderBy('created_at','DESC')->paginate($request->count);
        return ActivityResource::collection($data);
    }

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
            $id = $request->id;
        }else{
            $data = new UserRole;
            $data->role_id = $request->role_id;
            $data->user_id = $request->id;
            $data->added_by = \Auth::user()->id;
            $data->is_active = 1;
            $data->save();
            $id = $data->id;
        }

        $data = UserRole::with('role:id,name','added:id','added.profile:user_id,firstname,middlename,lastname,suffix_id','removed:id','removed.profile:user_id,firstname,middlename,lastname,suffix_id')->where('id',$id)->first();
        return [
            'data' => new RoleResource($data),
            'message' => 'User role remove was successful!', 
            'info' => "You've successfully updated the selected user."
        ];
    }

    public function file($request){
        $file = $request->file('file');
        $hashids = new Hashids('krad',10);
        $code = $hashids->encode($request->id);

        $folder = UserFolder::firstOrCreate(
            ['user_id' => $request->id, 'name' => 'Reference1']
        );
        
        $folder_id = $folder->id;
 
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $s3Path = $file->storeAs('oneportal/reference', $filename, 's3');

        $folderFile = UserFolderFile::create([
            'name' => $file->getClientOriginalName(),
            'path' => $s3Path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'status' => 'processing',
            'type_id' => 1,
            'folder_id' => $folder_id
        ]);

        try {
            $rekognition = new RekognitionClient([
                'version' => 'latest',
                'region'      => config('services.rekognition.region'),
                'credentials' => [
                    'key'    => config('services.rekognition.key'),
                    'secret' => config('services.rekognition.secret'),
                ],
            ]);

            $rekognition->indexFaces([
                'CollectionId' => config('services.rekognition.collection_id'),
                'Image' => [
                    'S3Object' => [
                        'Bucket' => config('services.rekognition.bucket'),
                        'Name' => $s3Path,
                    ],
                ],
                'ExternalImageId' => (string) $request->id, 
                'DetectionAttributes' => ['DEFAULT'],
            ]);
            $folderFile->update(['status' => 'processing']);
        } catch (\Exception $e) {
            \Log::error('Rekognition failed: '.$e->getMessage());
            dd($e->getMessage());
        }

        // UploaderJob::dispatch($folderFile);
        return [
            'data' => new FileResource($folderFile),
            'message' => 'File uploaded successfully!',
            'info' => "Your file has been uploaded and is now available."
        ];
    }
}
