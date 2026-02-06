<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'rent_per_month',
        'water_rate',
        'electric_rate',
        'late_fee_per_day',
        'payment_due_day',
        'apartment_name',
        'address',
        'province',
        'district',
        'subdistrict',
        'postal_code',
        'phone',
        'email',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'rent_per_month' => 'decimal:2',
        'water_rate' => 'decimal:2',
        'electric_rate' => 'decimal:2',
        'late_fee_per_day' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public static function getInstance()
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'rent_per_month' => 4000,
                'water_rate' => 25,
                'electric_rate' => 8,
                'late_fee_per_day' => 50,
                'payment_due_day' => '5',
                'apartment_name' => 'เจเจพาร์ทเมนต์',
            ]);
        }
        return $setting;
    }
}
