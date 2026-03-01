<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ล้างข้อมูลทั้งหมดก่อน (เรียงลำดับ FK)
        Schema::disableForeignKeyConstraints();

        DB::table('notifications')->truncate();
        DB::table('moveout_requests')->truncate();
        DB::table('repairs')->truncate();
        DB::table('receipts')->truncate();
        DB::table('bills')->truncate();
        DB::table('meter_readings')->truncate();
        DB::table('contracts')->truncate();
        DB::table('users')->truncate();
        DB::table('rooms')->truncate();
        DB::table('settings')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->call([
            AdminSeeder::class,   // Admin user + 40 rooms (4 floors x 10)
            RoomSeeder::class,    // ห้องว่างที่เหลือ (ไม่ใช่ 101, 205, 302)
            TenantSeeder::class,  // Tenant users + contracts + bills + repairs + notifications
        ]);
    }
}
