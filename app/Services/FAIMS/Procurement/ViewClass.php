<?php

namespace App\Services\FAIMS\Procurement;

use App\Services\DropdownClass;
use App\Models\Procurement;
use App\Http\Resources\FAIMS\Procurement\ProcurementResource;
use Illuminate\Support\Facades\Auth;


class ViewClass
{
    public function __construct( DropdownClass $dropdown){
        $this->dropdown = $dropdown;
    }

    public function procurements($request){
        $data = ProcurementResource::collection(
            Procurement::with('status')
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('code', 'LIKE', "%{$keyword}%")
                      ->orWhere('date', 'LIKE', "%{$keyword}%")
                      ->orWhere('created_at', 'LIKE', "%{$keyword}%")
                      ->orWhere('updated_at', 'LIKE', "%{$keyword}%");
            })
            ->orderBy('created_at','DESC')
            ->paginate($request->count)
        );
        return $data;
    }



}
