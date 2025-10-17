<?php

namespace App\Services\HumanResource\Credit;

use Carbon\Carbon;
use Hashids\Hashids;
use App\Models\User;
use App\Http\Resources\Hr\Credit\IndexResource;
use App\Http\Resources\Hr\Credit\ViewResource;

class ViewClass
{
    public function lists($request)
    {
        $data = IndexResource::collection(
            User::select('users.id')
                ->with([
                    'profile:user_id,firstname,middlename,lastname,suffix_id',
                    'organization.position',
                    'organization.status',
                    'credits' => function ($q) {
                        $q->where('year', Carbon::now()->year)->where('is_active',1);
                    },
                    'credits.leave',
                    'credits.logs.type',
                ])
                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->when($request->keyword, function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->whereHas('profile', function ($q2) use ($keyword) {
                            $q2->whereRaw('lastname LIKE ?', ["%{$keyword}%"]);
                        })
                        ->orWhere('username', 'like', "%{$keyword}%");
                    });
                })
                ->whereHas('organization', function ($query) {
                    $query->where('type_id', 15)
                        ->where('status_id', 2);
                })
                ->orderBy('user_profiles.lastname', 'ASC')
                ->paginate($request->count)
        );

        return $data;
    }

    public function view($code){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($code);

        $data = new ViewResource(
            User::query()->select('users.id')
            ->with([
                'profile:user_id,firstname,middlename,lastname,suffix_id',
                'organization.position',
                'organization.status',
                'credits' => function ($q) {
                    $q->where('year', Carbon::now()->year)->where('is_active',1);
                },
                'credits.leave',
                'credits.logs.type',
            ])
            ->where('id',$id)->first()
        );
        return $data;
    }
}
