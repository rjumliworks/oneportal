<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procurement extends Model
{
    protected $fillable = [
        'code',
        'date',
        'purpose',
        'title',
        'division_id',
        'section_id',
        'fund_cluster_id',
        'requested_by_id',
        'approved_by_id',
        'reawarded_count',
        'rebidded_count',
        'quotation_count',
        'status_id',
        'sub_status_id'
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\ListUnit', 'unit_id', 'id');
    }

    public function fund_cluster()
    {
        return $this->belongsTo('App\Models\ListDropdown', 'fund_cluster_id', 'id');
    }

    public function requested_by()
    {
        return $this->belongsTo('App\Models\User', 'requested_by_id')->with('profile');
    }

    public function approved_by()
    {
        return $this->belongsTo('App\Models\User', 'approved_by_id');
    }

    public function procurement_codes()
    {
        return $this->hasMany('App\Models\ProcurementCode', 'procurement_id');
    }
    
    public function status()
    {
        return $this->belongsTo('App\Models\ListStatus', 'status_id');
    }

    public function sub_status()
    {
        return $this->belongsTo('App\Models\ListStatus', 'status_id');
    }

    
    public static function generateProcurementNumber($date = null)
    {
        if ($date) {
            $year = date("y", strtotime($date));  // 'y' gives the last two digits of the year
            $month = date("m", strtotime($date));
        } else {
            $year = date("y", strtotime("now"));  // 'y' gives the last two digits of the year
            $month = date("m", strtotime("now"));
        }
    
        $count = self::whereYear('date', date("Y", strtotime($date ?? "now")))
                     ->whereMonth('date', $month)
                     ->count() + 1;
    
        return 'PR-' . $year . '-' . $month . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
