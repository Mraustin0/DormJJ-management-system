<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'floor',
        'status',
        'payment_status',
        'month_rate',
    ];

    public function contract()
    {
        return $this->hasOne(Contract::class, 'room_id')->latest();
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'room_id');
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class, 'room_id');
    }

    public function allMeterReadings()
    {
        return $this->hasMany(MeterReading::class, 'room_id')->orderBy('billing_month', 'asc');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'room_id');
    }
}
