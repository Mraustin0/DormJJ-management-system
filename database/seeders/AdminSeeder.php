<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Room;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'AdminJJ',
            'email' => 'admin@test.com',
            'password' => bcrypt('123456'),
            'admin_role' => 'admin',
        ]);

        for ($f = 1; $f <= 2; $f++) {
            for ($i = 1; $i <= 5; $i++) {
                Room::create([
                    'floor' => $f,
                    'room_number' => "{$f}0{$i}",
                    'status' => 'ว่าง',
                    'payment_status' => 'ค้างชำระ',
                ]);
            }
        }
        
    }
}
