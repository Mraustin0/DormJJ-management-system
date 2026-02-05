<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Room::truncate();
        Schema::enableForeignKeyConstraints();

        $rooms = [];

        for ($floor = 1; $floor <= 4; $floor++) {
            for ($num = 1; $num <= 10; $num++) {
                $isOccupied = rand(1, 100) > 30;
                $status = $isOccupied ? 'ไม่ว่าง' : 'ว่าง';
                $payment = $isOccupied ? (rand(0, 1) ? 'ชำระแล้ว' : 'ค้างชำระ') : null;

                $rooms[] = [
                    'room_number' => $floor . sprintf("%02d", $num),
                    'floor' => $floor,
                    'status' => $status,
                    'payment_status' => $payment,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Room::insert($rooms);
    }
}
