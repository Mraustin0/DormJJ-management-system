<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Contract;
use App\Models\MeterReading;
use App\Models\Bill;
use App\Models\Receipt;
use Carbon\Carbon;

class BillSeeder extends Seeder
{
    /**
     * รายชื่อผู้เช่าตัวอย่าง
     */
    private $tenantNames = [
        'สมชาย ใจดี',
        'สมหญิง รักเรียน',
        'วิชัย เก่งกาจ',
        'นารี สวยงาม',
        'ประเสริฐ มั่งมี',
        'จันทร์ฉาย แสงงาม',
        'พิชิต ชนะศึก',
        'ดวงใจ รักษ์ดี',
        'อนันต์ เจริญสุข',
        'พรทิพย์ งามตา',
        'สุรชัย แข็งแรง',
        'มาลี หอมหวาน',
        'ธนกร รวยทรัพย์',
        'วรรณา อ่อนหวาน',
        'ชัยวัฒน์ ก้าวหน้า',
        'ปราณี ยิ้มใส',
        'วีระ สุขสันต์',
        'ลัดดา ดอกไม้',
        'สมศักดิ์ กล้าหาญ',
        'น้ำฝน ใสสะอาด',
        'อดิศร เลิศล้ำ',
        'ขวัญใจ น่ารัก',
        'บุญมี โชคดี',
        'สุดา งามพริ้ง',
        'พงศ์พัฒน์ พัฒนา',
        'กัลยา ดวงดาว',
        'เทวา อินทร์แก้ว',
        'วันดี สดใส',
        'ณัฐพล ไชยยศ',
        'มณี สีทอง',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating mock data for bills...');

        // ดึงห้องทั้งหมด
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            $this->command->error('No rooms found! Please run RoomSeeder first.');
            return;
        }

        // สุ่มเลือก 30 ห้องให้มีผู้เช่า
        $occupiedRooms = $rooms->random(min(30, $rooms->count()));
        $tenantIndex = 0;

        foreach ($occupiedRooms as $room) {
            // 1. สร้าง Contract
            $contract = Contract::create([
                'room_id' => $room->id,
                'tenant_name' => $this->tenantNames[$tenantIndex % count($this->tenantNames)],
                'nid' => '1' . str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT),
                'phone' => '08' . rand(10000000, 99999999),
                'email' => 'tenant' . ($tenantIndex + 1) . '@example.com',
                'start_date' => Carbon::now()->subMonths(rand(1, 12)),
                'end_date' => Carbon::now()->addMonths(rand(6, 12)),
            ]);

            // อัปเดตสถานะห้อง (ใช้ภาษาไทยตาม dashboard)
            $room->update(['status' => 'ไม่ว่าง']);

            // 2. สร้าง Meter Readings และ Bills สำหรับ 3 เดือนล่าสุด
            for ($i = 2; $i >= 0; $i--) {
                $billingMonth = Carbon::now()->subMonths($i)->format('Y-m');

                // สุ่มค่ามิเตอร์
                $waterPrev = rand(100, 500);
                $waterCurr = $waterPrev + rand(5, 30);
                $waterUnit = $waterCurr - $waterPrev;

                $elecPrev = rand(1000, 5000);
                $elecCurr = $elecPrev + rand(50, 200);
                $elecUnit = $elecCurr - $elecPrev;

                // อัตราค่าน้ำ/ไฟ
                $waterRate = 18; // บาท/หน่วย
                $elecRate = 8;   // บาท/หน่วย
                $roomRate = $room->month_rate ?? 3500;

                $waterAmount = $waterUnit * $waterRate;
                $elecAmount = $elecUnit * $elecRate;
                $otherFees = rand(0, 1) ? rand(50, 200) : 0;
                $totalAmount = $roomRate + $waterAmount + $elecAmount + $otherFees;

                // กำหนดสถานะบิล (เดือนเก่าๆ มักจะชำระแล้ว)
                $status = 'pending';
                if ($i == 2) {
                    $status = rand(1, 10) > 2 ? 'paid' : 'overdue'; // 80% paid
                } elseif ($i == 1) {
                    $status = rand(1, 10) > 4 ? 'paid' : 'pending'; // 60% paid
                }
                // $i == 0 (เดือนปัจจุบัน) ส่วนใหญ่ยังไม่ชำระ

                // สร้าง MeterReading (ใช้ status เดียวกับ bill)
                MeterReading::create([
                    'room_id' => $room->id,
                    'billing_month' => $billingMonth,
                    'water_prev' => $waterPrev,
                    'water_curr' => $waterCurr,
                    'water_unit' => $waterUnit,
                    'elec_prev' => $elecPrev,
                    'elec_curr' => $elecCurr,
                    'elec_unit' => $elecUnit,
                    'total_amount' => $totalAmount,
                    'status' => $status,
                ]);

                // สร้าง Bill
                $bill = Bill::create([
                    'room_id' => $room->id,
                    'bill_date' => Carbon::parse($billingMonth)->startOfMonth(),
                    'billing_month' => $billingMonth,
                    'total_price' => $totalAmount,
                    'total_amount' => $totalAmount,
                    'water_units' => $waterUnit,
                    'water_amount' => $waterAmount,
                    'electric_units' => $elecUnit,
                    'electric_amount' => $elecAmount,
                    'room_rate' => $roomRate,
                    'other_fees' => $otherFees,
                    'due_date' => Carbon::parse($billingMonth)->endOfMonth(),
                    'status' => $status,
                ]);

                // 3. ถ้าสถานะ paid ให้สร้างใบเสร็จด้วย
                if ($status == 'paid') {
                    Receipt::create([
                        'receipt_number' => 'RCP' . Carbon::parse($billingMonth)->format('Ym') . str_pad($bill->id, 4, '0', STR_PAD_LEFT),
                        'receipt_date' => Carbon::parse($billingMonth)->addDays(rand(5, 25)),
                        'amount_paid' => $totalAmount,
                        'payment_method' => collect(['เงินสด', 'โอนเงิน', 'พร้อมเพย์'])->random(),
                        'bill_id' => $bill->id,
                        'received_by' => 1, // Admin
                    ]);
                }
            }

            $tenantIndex++;
        }

        // สรุปผล
        $this->command->info('Created ' . Contract::count() . ' contracts');
        $this->command->info('Created ' . MeterReading::count() . ' meter readings');
        $this->command->info('Created ' . Bill::count() . ' bills');
        $this->command->info('Created ' . Receipt::count() . ' receipts');
        $this->command->info('Mock data created successfully!');
    }
}
