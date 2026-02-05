<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    protected $fillable = [
        'room_id',
        'billing_month',
        'water_prev',
        'water_curr',
        'water_unit',
        'elec_prev',
        'elec_curr',
        'elec_unit',
        'total_amount',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
