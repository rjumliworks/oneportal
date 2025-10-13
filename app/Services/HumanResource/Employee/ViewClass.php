<?php

namespace App\Services\HumanResource\Employee;

use App\Models\ListData;

class ViewClass
{
    public function counts(){
        $statuses = ListData::where('is_active',1)->where('type','Employment Status')->get()->map(function ($item) {
            return [
                'value' => $item->id,
                'name' => $item->name,
                'count' => 0
                // UserOrganization::where('type_id',$item->id)->count()
            ];
        });
        return $statuses;
    }
}
