<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'is_active',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_staff');
    }

    public function workingHours()
    {
        return $this->hasMany(StaffWorkingHour::class);
    }
}
