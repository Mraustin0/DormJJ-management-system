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
                $rooms[] = [
                    'room_number' => $floor . sprintf("%02d", $num),
                    'floor' => $floor,
                    'status' => 'ว่าง',
                    'payment_status' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Room::insert($rooms);
    }
}
