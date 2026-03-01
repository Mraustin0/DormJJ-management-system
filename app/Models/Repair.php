<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    protected $fillable = [
        'room_id',
        'category',
        'title',
        'description',
        'image',
        'status',
        'remark',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}