<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTravelCode extends Model
{
    protected $fillable = [
        'code',
        'division_id',
        'travel_id'
    ];

    public function travel()
    {
        return $this->belongsTo('App\Models\RequestTravel', 'travel_id', 'id');
    }
}
