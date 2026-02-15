<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Room;
use App\Models\Contract;
use App\Models\MeterReading;
use App\Models\Bill;
use App\Models\Receipt;
use App\Models\Setting;
use Carbon\Carbon;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating tenant mock data...');

        // ตั้งค่า Settings ถ้ายังไม่มี
        $setting = Setting::getInstance();

        // ========================================
        // Tenant 1: สมชาย ไอดี (ห้อง 101)
        // ========================================
        $room101 = Room::where('room_number', '101')->first();
        if (!$room101) {
            $room101 = Room::create([
                'room_number' => '101',
                'floor' => 1,
                'status' => 'ไม่ว่าง',
                'payment_status' => 'ค้างชำระ',
                'month_rate' => 4800,
            ]);
        } else {
            $room101->update([
                'status' => 'ไม่ว่าง',
                'payment_status' => 'ค้างชำระ',
                'month_rate' => 4800,
            ]);
        }

        $tenantUser1 = User::create([
            'username' => 'somchai',
            'email' => 'somchai@jjapartment.com',
            'password' => bcrypt('123456'),
            'tenant_role' => 'tenant',
        ]);

        $contract1 = Contract::create([
            'room_id' => $room101->id,
            'user_id' => $tenantUser1->id,
            'tenant_name' => 'สมชาย ไอดี',
            'nid' => '1100700123456',
            'phone' => '0891234567',
            'email' => 'somchai@jjapartment.com',
            'emergency_contact_name' => 'สมหญิง ไอดี',
            'emergency_contact_phone' => '0899876543',
            'contract_duration' => 12,
            'check_in_date' => Carbon::now()->subMonths(8),
            'start_date' => Carbon::now()->subMonths(8),
            'end_date' => Carbon::now()->addMonths(4),
            'deposit' => 9600,
        ]);

        // สร้างข้อมูลมิเตอร์ + บิล 6 เดือนย้อนหลัง
        $this->createBillsForRoom($room101, $contract1, 6, $setting);

        // ========================================
        // Tenant 2: มานี รักเรียน (ห้อง 205)
        // ========================================
        $room205 = Room::where('room_number', '205')->first();
        if (!$room205) {
            $room205 = Room::create([
                'room_number' => '205',
                'floor' => 2,
                'status' => 'ไม่ว่าง',
                'payment_status' => 'ชำระแล้ว',
                'month_rate' => 4800,
            ]);
        } else {
            $room205->update([
                'status' => 'ไม่ว่าง',
                'payment_status' => 'ชำระแล้ว',
                'month_rate' => 4800,
            ]);
        }

        $tenantUser2 = User::create([
            'username' => 'manee',
            'email' => 'manee@example.com',
            'password' => bcrypt('123456'),
            'tenant_role' => 'tenant',
        ]);

        $contract2 = Contract::create([
            'room_id' => $room205->id,
            'user_id' => $tenantUser2->id,
            'tenant_name' => 'มานี รักเรียน',
            'nid' => '1100700654321',
            'phone' => '0812345678',
            'email' => 'manee@example.com',
            'emergency_contact_name' => 'มานะ รักเรียน',
            'emergency_contact_phone' => '0823456789',
            'contract_duration' => 12,
            'check_in_date' => Carbon::now()->subMonths(5),
            'start_date' => Carbon::now()->subMonths(5),
            'end_date' => Carbon::now()->addMonths(7),
            'deposit' => 9600,
        ]);

        $this->createBillsForRoom($room205, $contract2, 5, $setting, true);

        // ========================================
        // Tenant 3: วิชัย เก่งกาจ (ห้อง 302)
        // ========================================
        $room302 = Room::where('room_number', '302')->first();
        if (!$room302) {
            $room302 = Room::create([
                'room_number' => '302',
                'floor' => 3,
                'status' => 'ไม่ว่าง',
                'payment_status' => 'ค้างชำระ',
                'month_rate' => 4800,
            ]);
        } else {
            $room302->update([
                'status' => 'ไม่ว่าง',
                'payment_status' => 'ค้างชำระ',
                'month_rate' => 4800,
            ]);
        }

        $tenantUser3 = User::create([
            'username' => 'wichai',
            'email' => 'wichai@example.com',
            'password' => bcrypt('123456'),
            'tenant_role' => 'tenant',
        ]);

        $contract3 = Contract::create([
            'room_id' => $room302->id,
            'user_id' => $tenantUser3->id,
            'tenant_name' => 'วิชัย เก่งกาจ',
            'nid' => '1100700111222',
            'phone' => '0834567890',
            'email' => 'wichai@example.com',
            'emergency_contact_name' => 'วิไล เก่งกาจ',
            'emergency_contact_phone' => '0845678901',
            'contract_duration' => 6,
            'check_in_date' => Carbon::now()->subMonths(3),
            'start_date' => Carbon::now()->subMonths(3),
            'end_date' => Carbon::now()->addMonths(3),
            'deposit' => 9600,
        ]);

        $this->createBillsForRoom($room302, $contract3, 3, $setting);

        // ========================================
        // สรุปข้อมูล
        // ========================================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('  Mock Tenant Users Created!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('  Tenant 1 (ห้อง 101):');
        $this->command->info('    Username : somchai');
        $this->command->info('    Password : 123456');
        $this->command->info('    Email    : somchai@jjapartment.com');
        $this->command->info('    ชื่อ     : สมชาย ไอดี');
        $this->command->info('');
        $this->command->info('  Tenant 2 (ห้อง 205):');
        $this->command->info('    Username : manee');
        $this->command->info('    Password : 123456');
        $this->command->info('    Email    : manee@example.com');
        $this->command->info('    ชื่อ     : มานี รักเรียน');
        $this->command->info('');
        $this->command->info('  Tenant 3 (ห้อง 302):');
        $this->command->info('    Username : wichai');
        $this->command->info('    Password : 123456');
        $this->command->info('    Email    : wichai@example.com');
        $this->command->info('    ชื่อ     : วิชัย เก่งกาจ');
        $this->command->info('');
        $this->command->info('  Admin:');
        $this->command->info('    Username : AdminJJ');
        $this->command->info('    Password : 123456');
        $this->command->info('========================================');
    }

    /**
     * สร้าง MeterReadings + Bills + Receipts สำหรับห้อง
     */
    private function createBillsForRoom(Room $room, Contract $contract, int $months, Setting $setting, bool $allPaid = false): void
    {
        $waterRate = $setting->water_rate ?? 18;
        $electricRate = $setting->electric_rate ?? 8;
        $roomRate = $room->month_rate ?? 4800;

        // ค่าเริ่มต้นมิเตอร์
        $waterBase = rand(100, 300);
        $elecBase = rand(2000, 3000);

        for ($i = $months - 1; $i >= 0; $i--) {
            $billingMonth = Carbon::now()->subMonths($i)->format('Y-m');
            $billingDate = Carbon::parse($billingMonth);

            // สุ่มหน่วยใช้งาน
            $waterUnit = rand(3, 8);
            $elecUnit = rand(70, 220);

            $waterPrev = $waterBase;
            $waterCurr = $waterBase + $waterUnit;
            $waterBase = $waterCurr;

            $elecPrev = $elecBase;
            $elecCurr = $elecBase + $elecUnit;
            $elecBase = $elecCurr;

            $waterAmount = $waterUnit * $waterRate;
            $elecAmount = $elecUnit * $electricRate;
            $otherFees = ($i % 3 == 0) ? 260 : 0; // ค่าเพิ่มเติมทุก 3 เดือน
            $totalAmount = $roomRate + $waterAmount + $elecAmount + $otherFees;

            // กำหนดสถานะ: เดือนปัจจุบัน = pending, ที่เหลือ = paid
            if ($allPaid) {
                $status = 'paid';
            } else {
                $status = ($i == 0) ? 'pending' : 'paid';
            }

            // สร้าง MeterReading
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
                'bill_date' => $billingDate->copy()->startOfMonth(),
                'billing_month' => $billingMonth,
                'total_price' => $totalAmount,
                'total_amount' => $totalAmount,
                'water_units' => $waterUnit,
                'water_amount' => $waterAmount,
                'electric_units' => $elecUnit,
                'electric_amount' => $elecAmount,
                'room_rate' => $roomRate,
                'other_fees' => $otherFees,
                'due_date' => $billingDate->copy()->endOfMonth(),
                'status' => $status,
            ]);

            // สร้าง Receipt ถ้าชำระแล้ว
            if ($status == 'paid') {
                Receipt::create([
                    'receipt_number' => 'RCP' . $billingDate->format('Ym') . str_pad($bill->id, 4, '0', STR_PAD_LEFT),
                    'receipt_date' => $billingDate->copy()->addDays(rand(3, 15)),
                    'amount_paid' => $totalAmount,
                    'payment_method' => collect(['เงินสด', 'โอนเงิน', 'พร้อมเพย์'])->random(),
                    'bill_id' => $bill->id,
                    'received_by' => 1,
                ]);
            }
        }
    }
}
