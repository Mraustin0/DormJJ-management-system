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
        'status',
        'contract_file',
        'idcard_file',
        'deposit',
    ];

    protected function casts(): array
    {
        return [
            'start_date'    => 'date',
            'end_date'      => 'date',
            'check_in_date' => 'date',
            'contract_date' => 'date',
            'deposit'       => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** สัญญาที่กำลังใช้งานอยู่ (active หรือ ending) */
    public function scopeCurrent($query)
    {
        return $query->whereIn('status', ['active', 'ending']);
    }

    /** สัญญาที่หมดอายุแล้ว */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** ชื่อ status เป็นภาษาไทย */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'  => 'กำลังใช้งาน',
            'ending'  => 'ใกล้หมดสัญญา',
            'expired' => 'หมดสัญญา',
            default   => $this->status ?? '-',
        };
    }

    /** สี Tailwind สำหรับ badge (ใช้ตอน UI ต้องการ) */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'  => 'green',
            'ending'  => 'yellow',
            'expired' => 'red',
            default   => 'gray',
        };
    }
}
