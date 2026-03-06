<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // status: active (กำลังใช้งาน), ending (ใกล้หมดสัญญา <=30 วัน), expired (หมดสัญญาแล้ว)
            $table->enum('status', ['active', 'ending', 'expired'])
                  ->default('active')
                  ->after('deposit');
        });

        // อัปเดตสัญญาที่มีอยู่แล้ว: ถ้า end_date < วันนี้ → expired, ถ้าว่าง → active
        DB::statement("
            UPDATE contracts
            SET status = CASE
                WHEN end_date IS NOT NULL AND end_date < CURDATE() THEN 'expired'
                WHEN end_date IS NOT NULL AND end_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'ending'
                ELSE 'active'
            END
        ");
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
