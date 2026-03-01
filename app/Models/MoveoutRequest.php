<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoveoutRequest extends Model
{
    protected $fillable = [
        'contract_id',
        'room_id',
        'notify_date',
        'moveout_date',
        'status',
        'admin_remark',
        'deposit_return',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'notify_date' => 'date',
            'moveout_date' => 'date',
            'processed_at' => 'datetime',
            'deposit_return' => 'decimal:2',
        ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
