<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_category_id',
        'name',
        'description',
        'price',
        'duration',
        'cleanup_time',
        'is_active',
    ];

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'service_staff');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}
