<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ลบข้อมูลซ้ำก่อน แล้วเพิ่ม unique constraint บน (room_id, billing_month)
     */
    public function up(): void
    {
        // ลบแถวซ้ำ — เก็บแถวล่าสุด (id สูงสุด) ของแต่ละคู่ room_id + billing_month
        DB::statement('
            DELETE mr FROM meter_readings mr
            INNER JOIN (
                SELECT room_id, billing_month, MAX(id) AS keep_id
                FROM meter_readings
                GROUP BY room_id, billing_month
                HAVING COUNT(*) > 1
            ) dup ON mr.room_id = dup.room_id
                  AND mr.billing_month = dup.billing_month
                  AND mr.id <> dup.keep_id
        ');

        Schema::table('meter_readings', function (Blueprint $table) {
            $table->unique(['room_id', 'billing_month'], 'meter_readings_room_month_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropUnique('meter_readings_room_month_unique');
        });
    }
};
