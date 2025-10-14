<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAcademic extends Model
{
    protected $fillable = ['school','course','is_ongoing','level_id','user_id','graduated_at'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\ListData', 'level_id', 'id');
    }
}
