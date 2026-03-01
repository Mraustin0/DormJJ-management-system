<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_date',
        'billing_month',
        'total_price',
        'total_amount',
        'water_units',
        'water_amount',
        'electric_units',
        'electric_amount',
        'room_rate',
        'other_fees',
        'due_date',
        'status',
        'payment_slip',
        'room_id',
        'tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'bill_date' => 'date',
            'due_date'  => 'date',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'bill_id');
    }
}
