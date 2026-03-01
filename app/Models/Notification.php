<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Create a notification for a tenant user of a specific room
     */
    public static function notifyTenantByRoom($roomId, $type, $title, $message, $link = null)
    {
        $contract = \App\Models\Contract::where('room_id', $roomId)
            ->whereNotNull('user_id')
            ->latest()
            ->first();

        if ($contract && $contract->user_id) {
            return self::create([
                'user_id' => $contract->user_id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
            ]);
        }

        return null;
    }
}
