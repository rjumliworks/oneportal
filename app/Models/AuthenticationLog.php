<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthenticationLog extends Model
{
    use HasFactory;

    protected $table = 'authentication_logs';

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'authenticatable_id', 'id');
    }
}
