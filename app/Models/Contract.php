<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'room_id',
        'user_id',
        'tenant_name',
        'nid',
        'phone',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'contract_duration',
        'check_in_date',
        'contract_date',
        'start_date',
        'end_date',
        'contract_file',
        'idcard_file',
        'deposit',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'check_in_date' => 'date',
            'contract_date' => 'date',
            'deposit' => 'decimal:2',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
