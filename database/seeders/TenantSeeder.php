<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Room;
use App\Models\Contract;
use App\Models\MeterReading;
use App\Models\Bill;
use App\Models\Receipt;
use App\Models\Repair;
use App\Models\Notification;
use App\Models\MoveoutRequest;
use App\Models\Setting;
use Carbon\Carbon;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating tenant mock data...');

        // ตั้งค่า Settings
        $setting = Setting::first();
        if (!$setting) {
            $setting = Setting::create([
                'apartment_name'  => 'JJ Apartment',
                'rent_per_month'  => 4800,
                'water_rate'      => 18,
                'electric_rate'   => 4,
                'late_fee_per_day'=> 50,
                'payment_due_day' => '5',
                'address'         => '123 ถนนสุขุมวิท',
                'subdistrict'     => 'คลองเตย',
                'district'        => 'คลองเตย',
                'province'        => 'กรุงเทพมหานคร',
                'postal_code'     => '10110',
                'phone'           => '02-123-4567',
                'email'           => 'jjapartment@gmail.com',
                'latitude'        => 13.7563,
                'longitude'       => 100.5018,
            ]);
        }

        // ===================================================================
        // TENANT 1: สมชาย ใจดี (ห้อง 101)
        // - มีบิลค้างชำระเดือนล่าสุด
        // - มีประวัติบิล 6 เดือน
        // - มีการแจ้งซ่อม (pending/in_progress/completed)
        // - มีการแจ้งเตือน
        // ===================================================================
        $room101 = Room::where('room_number', '101')->firstOrFail();
        $room101->update(['status' => 'ไม่ว่าง', 'payment_status' => 'ค้างชำระ', 'month_rate' => 4800]);

        $user1 = User::create([
            'username'    => 'somchai',
            'email'       => 'somchai@jjapartment.com',
            'password'    => bcrypt('123456'),
            'tenant_role' => 'tenant',
        ]);

        $contract1 = Contract::create([
            'room_id'                  => $room101->id,
            'user_id'                  => $user1->id,
            'tenant_name'              => 'สมชาย ใจดี',
            'nid'                      => '1100700123456',
            'phone'                    => '0891234567',
            'email'                    => 'somchai@jjapartment.com',
            'emergency_contact_name'   => 'สมหญิง ใจดี',
            'emergency_contact_phone'  => '0899876543',
            'contract_duration'        => 12,
            'check_in_date'            => Carbon::now()->subMonths(8)->startOfMonth(),
            'start_date'               => Carbon::now()->subMonths(8)->startOfMonth(),
            'end_date'                 => Carbon::now()->addMonths(4)->endOfMonth(),
            'deposit'                  => 9600,
        ]);

        // บิล 6 เดือน (5 เดือนเก่า = paid, เดือนปัจจุบัน = pending)
        $this->createBillsForRoom($room101, $contract1, 6, $setting, false);

        // แจ้งซ่อม - 3 รายการ สถานะต่างกัน
        Repair::create([
            'room_id'     => $room101->id,
            'category'    => 'ระบบไฟฟ้า',
            'title'       => 'ไฟในห้องน้ำดับ',
            'description' => 'หลอดไฟในห้องน้ำดับ เปลี่ยนหลอดแล้วยังไม่ติด อาจเป็นปัญหาที่สวิทช์',
            'status'      => 'pending',
            'created_at'  => Carbon::now()->subDays(2),
            'updated_at'  => Carbon::now()->subDays(2),
        ]);

        Repair::create([
            'room_id'     => $room101->id,
            'category'    => 'ระบบประปา',
            'title'       => 'ก๊อกน้ำรั่ว',
            'description' => 'ก๊อกน้ำในห้องน้ำรั่ว มีน้ำหยดตลอดเวลา',
            'status'      => 'in_progress',
            'remark'      => 'ช่างนัดวันพุธ',
            'created_at'  => Carbon::now()->subDays(7),
            'updated_at'  => Carbon::now()->subDays(3),
        ]);

        Repair::create([
            'room_id'     => $room101->id,
            'category'    => 'ประตูและหน้าต่าง',
            'title'       => 'ประตูห้องปิดไม่สนิท',
            'description' => 'บานพับประตูห้องหลวม ปิดแล้วไม่ล็อค',
            'status'      => 'completed',
            'remark'      => 'เปลี่ยนบานพับแล้ว',
            'created_at'  => Carbon::now()->subDays(20),
            'updated_at'  => Carbon::now()->subDays(15),
        ]);

        // แจ้งเตือน
        Notification::create([
            'user_id'    => $user1->id,
            'type'       => 'bill_created',
            'title'      => 'บิลประจำเดือนออกแล้ว',
            'message'    => 'บิลประจำเดือน ' . Carbon::now()->format('m/Y') . ' ออกแล้ว กรุณาชำระภายในกำหนด',
            'link'       => '/tenant/bills',
            'is_read'    => false,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        Notification::create([
            'user_id'    => $user1->id,
            'type'       => 'repair_in_progress',
            'title'      => 'กำลังดำเนินการซ่อม',
            'message'    => 'การแจ้งซ่อม "ก๊อกน้ำรั่ว" กำลังดำเนินการ ช่างจะเข้าซ่อมวันพุธนี้',
            'link'       => '/tenant/repairs',
            'is_read'    => false,
            'created_at' => Carbon::now()->subDays(3),
        ]);

        Notification::create([
            'user_id'    => $user1->id,
            'type'       => 'repair_completed',
            'title'      => 'แจ้งซ่อมเสร็จสิ้น',
            'message'    => 'การแจ้งซ่อม "ประตูห้องปิดไม่สนิท" ดำเนินการเสร็จสิ้นแล้ว',
            'link'       => '/tenant/repairs',
            'is_read'    => true,
            'created_at' => Carbon::now()->subDays(15),
        ]);

        // ===================================================================
        // TENANT 2: มานี รักเรียน (ห้อง 205)
        // - ชำระครบทุกเดือน (allPaid)
        // - มีการแจ้งซ่อม 1 รายการ (completed)
        // - มีการแจ้งเตือน 1 รายการ
        // ===================================================================
        $room205 = Room::where('room_number', '205')->firstOrFail();
        $room205->update(['status' => 'ไม่ว่าง', 'payment_status' => 'ชำระแล้ว', 'month_rate' => 4800]);

        $user2 = User::create([
            'username'    => 'manee',
            'email'       => 'manee@example.com',
            'password'    => bcrypt('123456'),
            'tenant_role' => 'tenant',
        ]);

        $contract2 = Contract::create([
            'room_id'                  => $room205->id,
            'user_id'                  => $user2->id,
            'tenant_name'              => 'มานี รักเรียน',
            'nid'                      => '1100700654321',
            'phone'                    => '0812345678',
            'email'                    => 'manee@example.com',
            'emergency_contact_name'   => 'มานะ รักเรียน',
            'emergency_contact_phone'  => '0823456789',
            'contract_duration'        => 12,
            'check_in_date'            => Carbon::now()->subMonths(5)->startOfMonth(),
            'start_date'               => Carbon::now()->subMonths(5)->startOfMonth(),
            'end_date'                 => Carbon::now()->addMonths(7)->endOfMonth(),
            'deposit'                  => 9600,
        ]);

        $this->createBillsForRoom($room205, $contract2, 5, $setting, true);

        Repair::create([
            'room_id'     => $room205->id,
            'category'    => 'เครื่องใช้และอุปกรณ์ในห้อง',
            'title'       => 'แอร์ไม่เย็น',
            'description' => 'แอร์เปิดแล้วลมออกแต่ไม่เย็น',
            'status'      => 'completed',
            'remark'      => 'เติมน้ำยาแอร์แล้ว',
            'created_at'  => Carbon::now()->subDays(30),
            'updated_at'  => Carbon::now()->subDays(25),
        ]);

        Notification::create([
            'user_id'    => $user2->id,
            'type'       => 'payment_confirmed',
            'title'      => 'ยืนยันการชำระเงินแล้ว',
            'message'    => 'แอดมินยืนยันการชำระค่าเช่าเดือน ' . Carbon::now()->subMonths(1)->format('m/Y') . ' เรียบร้อยแล้ว',
            'link'       => '/tenant/bills',
            'is_read'    => true,
            'created_at' => Carbon::now()->subMonths(1)->addDays(7),
        ]);

        // ===================================================================
        // TENANT 3: วิชัย เก่งกาจ (ห้อง 302)
        // - มีบิลค้างชำระ
        // - มีการแจ้งย้ายออก (pending)
        // - มีการแจ้งซ่อม 1 รายการ
        // ===================================================================
        $room302 = Room::where('room_number', '302')->firstOrFail();
        $room302->update(['status' => 'ไม่ว่าง', 'payment_status' => 'ค้างชำระ', 'month_rate' => 4800]);

        $user3 = User::create([
            'username'    => 'wichai',
            'email'       => 'wichai@example.com',
            'password'    => bcrypt('123456'),
            'tenant_role' => 'tenant',
        ]);

        $contract3 = Contract::create([
            'room_id'                  => $room302->id,
            'user_id'                  => $user3->id,
            'tenant_name'              => 'วิชัย เก่งกาจ',
            'nid'                      => '1100700111222',
            'phone'                    => '0834567890',
            'email'                    => 'wichai@example.com',
            'emergency_contact_name'   => 'วิไล เก่งกาจ',
            'emergency_contact_phone'  => '0845678901',
            'contract_duration'        => 6,
            'check_in_date'            => Carbon::now()->subMonths(3)->startOfMonth(),
            'start_date'               => Carbon::now()->subMonths(3)->startOfMonth(),
            'end_date'                 => Carbon::now()->addMonths(3)->endOfMonth(),
            'deposit'                  => 9600,
        ]);

        $this->createBillsForRoom($room302, $contract3, 3, $setting, false);

        Repair::create([
            'room_id'     => $room302->id,
            'category'    => 'พื้นผนังฝ้า',
            'title'       => 'ผนังมีรอยน้ำรั่ว',
            'description' => 'มีคราบน้ำที่ผนังด้านหลังห้องน้ำ',
            'status'      => 'pending',
            'created_at'  => Carbon::now()->subDays(5),
            'updated_at'  => Carbon::now()->subDays(5),
        ]);

        // แจ้งย้ายออก (pending)
        MoveoutRequest::create([
            'contract_id' => $contract3->id,
            'room_id'     => $room302->id,
            'notify_date' => Carbon::now()->subDays(3)->toDateString(),
            'moveout_date'=> Carbon::now()->addDays(27)->toDateString(),
            'status'      => 'pending',
        ]);

        Notification::create([
            'user_id'    => $user3->id,
            'type'       => 'moveout_processed',
            'title'      => 'รับเรื่องแจ้งย้ายออกแล้ว',
            'message'    => 'ระบบรับเรื่องแจ้งย้ายออกของคุณแล้ว รอแอดมินตรวจสอบ',
            'link'       => '/tenant/moveout/status',
            'is_read'    => false,
            'created_at' => Carbon::now()->subDays(3),
        ]);

        // ===================================================================
        // สรุป
        // ===================================================================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('  Mock Data Created!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('  Admin:');
        $this->command->info('    Username : AdminJJ    Password : 123456');
        $this->command->info('');
        $this->command->info('  Tenant 1 - ห้อง 101 (บิลค้างชำระ + แจ้งซ่อม)');
        $this->command->info('    Username : somchai    Password : 123456');
        $this->command->info('');
        $this->command->info('  Tenant 2 - ห้อง 205 (ชำระครบ)');
        $this->command->info('    Username : manee      Password : 123456');
        $this->command->info('');
        $this->command->info('  Tenant 3 - ห้อง 302 (บิลค้างชำระ + แจ้งย้ายออก)');
        $this->command->info('    Username : wichai     Password : 123456');
        $this->command->info('========================================');
    }

    private function createBillsForRoom(Room $room, Contract $contract, int $months, Setting $setting, bool $allPaid = false): void
    {
        $waterRate   = $setting->water_rate ?? 18;
        $electricRate= $setting->electric_rate ?? 4;
        $roomRate    = $room->month_rate ?? 4800;

        $waterBase = rand(100, 300);
        $elecBase  = rand(2000, 3000);

        for ($i = $months - 1; $i >= 0; $i--) {
            $billingMonth = Carbon::now()->subMonths($i)->format('Y-m');
            $billingDate  = Carbon::parse($billingMonth . '-01');

            $waterUnit = rand(3, 10);
            $elecUnit  = rand(80, 200);

            $waterPrev = $waterBase;
            $waterCurr = $waterBase + $waterUnit;
            $waterBase = $waterCurr;

            $elecPrev = $elecBase;
            $elecCurr = $elecBase + $elecUnit;
            $elecBase = $elecCurr;

            $waterAmount = $waterUnit * $waterRate;
            $elecAmount  = $elecUnit * $electricRate;
            $otherFees   = ($i % 3 == 0 && $i != 0) ? 260 : 0;
            $totalAmount = $roomRate + $waterAmount + $elecAmount + $otherFees;

            // เดือนปัจจุบัน = pending ถ้าไม่ใช่ allPaid
            $status = $allPaid ? 'paid' : (($i == 0) ? 'pending' : 'paid');

            MeterReading::create([
                'room_id'       => $room->id,
                'billing_month' => $billingMonth,
                'water_prev'    => $waterPrev,
                'water_curr'    => $waterCurr,
                'water_unit'    => $waterUnit,
                'elec_prev'     => $elecPrev,
                'elec_curr'     => $elecCurr,
                'elec_unit'     => $elecUnit,
                'total_amount'  => $totalAmount,
                'status'        => $status,
            ]);

            $bill = Bill::create([
                'room_id'         => $room->id,
                'bill_date'       => $billingDate->copy()->startOfMonth(),
                'billing_month'   => $billingMonth,
                'total_price'     => $totalAmount,
                'total_amount'    => $totalAmount,
                'water_units'     => $waterUnit,
                'water_amount'    => $waterAmount,
                'electric_units'  => $elecUnit,
                'electric_amount' => $elecAmount,
                'room_rate'       => $roomRate,
                'other_fees'      => $otherFees,
                'due_date'        => $billingDate->copy()->addDays((int)($setting->payment_due_day ?? 5) + 25),
                'status'          => $status,
            ]);

            if ($status === 'paid') {
                Receipt::create([
                    'receipt_number' => 'RCP' . $billingDate->format('Ym') . str_pad($bill->id, 4, '0', STR_PAD_LEFT),
                    'receipt_date'   => $billingDate->copy()->addDays(rand(3, 12)),
                    'amount_paid'    => $totalAmount,
                    'payment_method' => collect(['เงินสด', 'โอนเงิน', 'พร้อมเพย์'])->random(),
                    'bill_id'        => $bill->id,
                    'received_by'    => 1,
                ]);
            }
        }
    }
}
