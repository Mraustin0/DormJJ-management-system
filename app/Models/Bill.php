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

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'bill_id');
    }

    /** จำนวนวันที่เลยกำหนดชำระ (0 ถ้ายังไม่เลย) */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->due_date || in_array($this->status, ['paid', 'reviewing'])) {
            return 0;
        }
        $days = (int) now()->startOfDay()->diffInDays($this->due_date->copy()->startOfDay(), false);
        return max(0, -$days);
    }

    /** ค่าปรับล่าช้า = วันที่เลยกำหนด × late_fee_per_day จาก Settings */
    public function getLateFeeAttribute(): float
    {
        if ($this->days_overdue === 0) {
            return 0.0;
        }
        $rate = (float) (\App\Models\Setting::getInstance()->late_fee_per_day ?? 0);
        return $this->days_overdue * $rate;
    }
}
