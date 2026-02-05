<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\Contract;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        // ลบข้อมูลสัญญาเก่าก่อน กันซ้ำ
        DB::table('contracts')->truncate();

        // ดึงห้องที่ "ไม่ว่าง" ทั้งหมดมา
        $occupiedRooms = Room::where('status', 'ไม่ว่าง')->get();

        $faker = \Faker\Factory::create('th_TH'); // ใช้ชื่อคนไทย

        foreach ($occupiedRooms as $room) {
            Contract::create([
                'roomId' => $room->roomId, // เชื่อมกับห้อง
                'tenant_name' => $faker->name, // สุ่มชื่อไทย
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'), // สุ่มวันทำสัญญา
                // ถ้าใน Table contracts คุณมี field อื่นๆ เช่น เบอร์โทร, เลขบัตร ให้ใส่เพิ่มตรงนี้
            ]);
        }
    }
}