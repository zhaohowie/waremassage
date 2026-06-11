<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'customer_id',
        'service_id',
        'staff_id',
        'appointment_date',
        'appointment_time',
        'duration',
        'price',
        'notes',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function soapNotes()
    {
        return $this->hasMany(SoapNote::class);
    }

    public function activities()
    {
        return $this->hasMany(AppointmentActivity::class);
    }
}
