<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'receipt_date',
        'amount_paid',
        'payment_method',
        'payment_slip',
        'notes',
        'bill_id',
        'received_by',
    ];

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
        ];
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * สร้างเลขที่ใบเสร็จอัตโนมัติ
     */
    public static function generateReceiptNumber()
    {
        $prefix = 'RCP';
        $year = date('Y');
        $month = date('m');

        // หาเลขที่ใบเสร็จล่าสุดของเดือนนี้
        $lastReceipt = self::whereYear('created_at', $year)
                          ->whereMonth('created_at', $month)
                          ->orderBy('id', 'desc')
                          ->first();

        if ($lastReceipt) {
            // ดึงเลขลำดับจากใบเสร็จล่าสุด
            $lastNumber = (int) substr($lastReceipt->receipt_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
