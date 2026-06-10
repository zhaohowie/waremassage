<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoapNote extends Model
{
    protected $fillable = [
        'appointment_id',
        'staff_id',
        'subjective',
        'objective',
        'assessment',
        'plan',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
