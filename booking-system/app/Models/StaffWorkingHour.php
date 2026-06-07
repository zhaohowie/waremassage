<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffWorkingHour extends Model
{
    protected $fillable = [
        'staff_id',
        'schedule_type',
        'day_of_week',
        'specific_date',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'is_available',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}