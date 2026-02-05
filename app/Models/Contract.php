<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'room_id',
        'tenant_name',
        'nid',
        'phone',
        'email',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
