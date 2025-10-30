<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrgChart extends Model
{
    protected $fillable = ['user_id','oic_id','is_oic','is_active'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function oic()
    {
        return $this->belongsTo('App\Models\User', 'oic_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo('App\Models\ListDropdown', 'designation_id', 'id');
    }

    public function assigned()
    {
        return $this->belongsTo('App\Models\ListDropdown', 'assigned_id', 'id');
    }

    public function designationable()
    {
        return $this->morphOne('App\Models\OrgSignatory', 'designationable');
    }
}
