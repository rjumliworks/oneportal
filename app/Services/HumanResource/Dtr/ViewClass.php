<?php

namespace App\Services\HumanResource\Dtr;

use App\Models\Dtr;
use App\Http\Resources\Hr\Dtr\IndexResource;

class ViewClass
{
    public function list($request){
        $data = IndexResource::collection(
            Dtr::with('user:id,email,username','user.profile:user_id,firstname,middlename,lastname,suffix_id')
            ->when($request->keyword, function ($query, $keyword) {
                $query->whereHas('user',function ($query) use ($keyword) {
                    $query->whereHas('profile',function ($query) use ($keyword) {
                        $query->when($keyword, function ($query, $keyword) {
                            $query->whereRaw('lastname LIKE ?', ['%' . $keyword . '%']);
                        });
                    });
                });
            })
            ->when($request->date, function ($query, $date) {
                $query ->where('date',$date);
            })
            ->orderBy('created_At', 'DESC')
            ->paginate($request->count)
        );
        return $data;
    }
}
