<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_date',
        'total_price',
        'status',
        'room_id',
        'tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'bill_date' => 'date',
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
}
