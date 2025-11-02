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
}
