<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeterReading;
use App\Models\Notification;
use App\Models\Room;
use App\Models\Bill;
use App\Models\Receipt;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index(Request $request)
    {
        // 1. สร้าง Query เตรียมดึงข้อมูลจาก Bill (พร้อมข้อมูลห้องและใบเสร็จ)
        $query = Bill::with(['room.contract', 'receipt']);

        // 2. กรองข้อมูล: ตามเดือน (ถ้ามีการเลือก)
        $selectedMonth = $request->input('month');
        if ($selectedMonth && $selectedMonth != 'all') {
            $query->where('billing_month', $selectedMonth);
        }

        // 3. กรองข้อมูล: ตามสถานะ (ถ้ามีการเลือก)
        $selectedStatus = $request->input('status');
        if ($selectedStatus && $selectedStatus != 'all') {
            $query->where('status', $selectedStatus);
        }

        // 4. กรองข้อมูล: ค้นหาห้อง (Search)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('room', function($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%");
            });
        }

        // 5. ดึงข้อมูลและแบ่งหน้า (Pagination) ทีละ 10 รายการ
        $bills = $query->latest('billing_month') // เรียงเดือนล่าสุดขึ้นก่อน
                       ->orderBy('room_id')      // เรียงตามห้อง
                       ->paginate(10)            // ตัดหน้าทีละ 10
                       ->appends($request->all()); // คงค่า Filter ไว้ตอนกดเปลี่ยนหน้า

        // 6. สร้างตัวเลือกเดือนย้อนหลัง 12 เดือน สำหรับ Dropdown
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }

        // 7. ตัวเลือกสถานะ
        $statuses = [
            'pending' => 'รอชำระ',
            'reviewing' => 'รอการอนุมัติ',
            'paid' => 'ชำระแล้ว',
            'overdue' => 'ค้างชำระ',
        ];

        // ดึง bills ทั้งหมดที่มีใบเสร็จ สำหรับ download modal (ไม่ paginate)
        $paidBills = Bill::with(['room.contract', 'receipt'])
            ->whereHas('receipt')
            ->orderBy('billing_month', 'desc')
            ->get();

        return view('rooms.bills', compact('bills', 'months', 'selectedMonth', 'statuses', 'selectedStatus', 'paidBills'));
    }

    /**
     * หน้าเลือกห้องสำหรับสร้างบิล
     */
    public function create(Request $request)
    {
        $currentFloor = $request->query('floor', 1);
        $selectedMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $search = $request->query('search');

        // ดึงห้องตามชั้นที่เลือก พร้อม contract และ meter readings
        $query = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }]);

        // ถ้ามีการค้นหา ให้ค้นหาทุกชั้น
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhereHas('contract', function($q2) use ($search) {
                      $q2->where('tenant_name', 'like', "%{$search}%");
                  });
            });
        } else {
            $query->where('floor', $currentFloor);
        }

        $rooms = $query->orderBy('room_number', 'asc')->get();

        // ดึง room_id ที่มีบิลสำหรับเดือนนี้แล้ว
        $existingBillRoomIds = Bill::where('billing_month', $selectedMonth)
            ->pluck('room_id')
            ->toArray();

        // ดึงห้องที่มีผู้เช่าทั้งหมด สำหรับ modal สร้างบิลทั้งหมด
        $setting = Setting::getInstance();
        $allOccupiedRooms = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }])->where('status', 'ไม่ว่าง')->orderBy('floor')->orderBy('room_number')->get();

        $allRoomsData = $allOccupiedRooms->map(function($room) use ($setting, $existingBillRoomIds) {
            $meter = $room->meterReadings->first();
            $elecUnits = $meter ? floatval($meter->elec_unit ?? 0) : 0;
            $waterUnits = $meter ? floatval($meter->water_unit ?? 0) : 0;
            $elecAmount = round($elecUnits * floatval($setting->electric_rate ?? 0), 2);
            $waterAmount = round($waterUnits * floatval($setting->water_rate ?? 0), 2);
            $roomRate = floatval($setting->rent_per_month ?? 0);
            return [
                'room_id'         => $room->id,
                'room_number'     => $room->room_number,
                'floor'           => $room->floor,
                'tenant_name'     => $room->contract ? $room->contract->tenant_name : '',
                'has_meter'       => $meter !== null,
                'electric_units'  => $elecUnits,
                'electric_amount' => $elecAmount,
                'water_units'     => $waterUnits,
                'water_amount'    => $waterAmount,
                'room_rate'       => $roomRate,
                'other_fees'      => 0,
                'has_bill'        => in_array($room->id, $existingBillRoomIds),
            ];
        })->values()->toArray();

        return view('rooms.bills_create', compact(
            'rooms', 'currentFloor', 'selectedMonth', 'search',
            'existingBillRoomIds', 'allRoomsData', 'setting'
        ));
    }

    /**
     * หน้าสร้างบิลสำหรับห้องที่เลือก
     */
    public function createForRoom(Request $request, $id)
    {
        $selectedMonth = $request->query('month', Carbon::now()->format('Y-m'));

        $room = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }])->findOrFail($id);

        // ตรวจว่ามีบิลของเดือนนี้อยู่แล้วไหม (billing_month เก็บเป็น string Y-m เช่น "2026-03")
        $existingBill = Bill::where('room_id', $id)
            ->where('billing_month', $selectedMonth)
            ->first();

        $setting = Setting::getInstance();

        return view('rooms.bill_form', compact('room', 'selectedMonth', 'existingBill', 'setting'));
    }

    /**
     * บันทึกบิล
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'billing_month' => 'required|date_format:Y-m',
            'total_amount' => 'required|numeric|min:0|max:999999',
        ]);

        // ตรวจสอบว่ามีข้อมูลมิเตอร์สำหรับเดือนนี้แล้วหรือยัง
        $meterExists = MeterReading::where('room_id', $request->room_id)
            ->where('billing_month', $request->billing_month)
            ->exists();
        if (!$meterExists) {
            return response()->json(['success' => false, 'message' => 'ยังไม่มีข้อมูลมิเตอร์สำหรับเดือนนี้ กรุณาบันทึกค่ามิเตอร์ก่อน'], 422);
        }

        // ตรวจสอบว่าบิลที่มีอยู่แล้วชำระแล้วหรือไม่
        $existingBill = Bill::where('room_id', $request->room_id)
            ->where('billing_month', $request->billing_month)
            ->first();
        if ($existingBill && in_array($existingBill->status, ['paid', 'reviewing'])) {
            return response()->json(['success' => false, 'message' => 'ไม่สามารถแก้ไขบิลที่รอการอนุมัติหรือชำระแล้วได้'], 422);
        }

        // สร้างหรืออัปเดตบิล
        $bill = Bill::updateOrCreate(
            [
                'room_id' => $request->room_id,
                'billing_month' => $request->billing_month,
            ],
            [
                'bill_date'      => now()->toDateString(),
                'total_price'    => $request->total_amount,
                'water_units'    => $request->water_units ?? 0,
                'water_amount'   => $request->water_amount ?? 0,
                'electric_units' => $request->electric_units ?? 0,
                'electric_amount'=> $request->electric_amount ?? 0,
                'room_rate'      => $request->room_rate ?? 0,
                'other_fees'     => $request->other_fees ?? 0,
                'due_date'       => $request->due_date ?? null,
                'total_amount'   => $request->total_amount,
                'status'         => 'pending',
            ]
        );

        // Send notification to tenant
        $room = Room::find($request->room_id);

        // เมื่อสร้างบิลใหม่ ให้รีเซ็ต payment_status ของห้องเป็น ค้างชำระ
        if ($bill->wasRecentlyCreated && $room) {
            $room->update(['payment_status' => 'ค้างชำระ']);
        }
        $monthLabel = Carbon::parse($request->billing_month)->translatedFormat('F Y');
        Notification::notifyTenantByRoom(
            $request->room_id,
            'bill_created',
            'บิลค่าเช่าประจำเดือน ' . $monthLabel,
            'มีบิลค่าเช่าห้อง ' . ($room->room_number ?? '') . ' จำนวน ' . number_format($request->total_amount, 2) . ' บาท',
            route('tenant.bills.view', $bill->id)
        );

        return response()->json(['success' => true, 'message' => 'บันทึกบิลเรียบร้อยแล้ว']);
    }

    /**
     * สร้างบิลทั้งหมดอัตโนมัติ
     */
    public function storeAll(Request $request)
    {
        $request->validate([
            'billing_month' => 'required|date_format:Y-m',
        ]);

        $billingMonth = $request->billing_month;
        $setting = Setting::getInstance();
        $count = 0;
        $monthLabel = Carbon::parse($billingMonth)->translatedFormat('F Y');
        $dueDate = Carbon::parse($billingMonth)->day($setting->payment_due_day ?? 5)->format('Y-m-d');

        $roomsInput = $request->input('rooms', []);

        if (!empty($roomsInput)) {
            // โหมด modal — รับข้อมูลต่อห้องที่ผู้ใช้แก้ไขแล้ว
            foreach ($roomsInput as $rd) {
                $roomId = $rd['room_id'] ?? null;
                if (!$roomId) continue;

                $room = Room::with('contract')->find($roomId);
                if (!$room || !$room->contract) continue;

                // ตรวจสอบว่ามีข้อมูลมิเตอร์ก่อนสร้างบิล
                $meterExists = MeterReading::where('room_id', $roomId)
                    ->where('billing_month', $billingMonth)
                    ->exists();
                if (!$meterExists) continue;

                // ข้ามห้องที่บิลชำระแล้วหรือรอการอนุมัติ
                $lockedBill = Bill::where('room_id', $roomId)
                    ->where('billing_month', $billingMonth)
                    ->whereIn('status', ['paid', 'reviewing'])
                    ->exists();
                if ($lockedBill) continue;

                $elecUnits  = floatval($rd['electric_units']  ?? 0);
                $waterUnits = floatval($rd['water_units']     ?? 0);
                $elecAmt    = floatval($rd['electric_amount'] ?? 0);
                $waterAmt   = floatval($rd['water_amount']    ?? 0);
                $roomRate   = floatval($rd['room_rate']       ?? 0);
                $otherFees  = floatval($rd['other_fees']      ?? 0);
                $total      = $roomRate + $elecAmt + $waterAmt + $otherFees;

                $bill = Bill::updateOrCreate(
                    ['room_id' => $roomId, 'billing_month' => $billingMonth],
                    [
                        'bill_date'       => now()->toDateString(),
                        'electric_units'  => $elecUnits,
                        'electric_amount' => $elecAmt,
                        'water_units'     => $waterUnits,
                        'water_amount'    => $waterAmt,
                        'room_rate'       => $roomRate,
                        'other_fees'      => $otherFees,
                        'total_price'     => $total,
                        'total_amount'    => $total,
                        'due_date'        => $dueDate,
                        'status'          => 'pending',
                    ]
                );

                // เมื่อสร้างบิลใหม่ ให้รีเซ็ต payment_status ของห้องเป็น ค้างชำระ
                if ($bill->wasRecentlyCreated) {
                    $room->update(['payment_status' => 'ค้างชำระ']);
                }

                Notification::notifyTenantByRoom(
                    $roomId, 'bill_created',
                    'บิลค่าเช่าประจำเดือน ' . $monthLabel,
                    'มีบิลค่าเช่าห้อง ' . $room->room_number . ' จำนวน ' . number_format($total, 2) . ' บาท',
                    route('tenant.bills')
                );
                $count++;
            }
        } else {
            // โหมด auto — คำนวณจากมิเตอร์และ settings
            $rooms = Room::with(['contract', 'meterReadings' => function($q) use ($billingMonth) {
                $q->where('billing_month', $billingMonth);
            }])->where('status', 'ไม่ว่าง')->get();

            foreach ($rooms as $room) {
                if (!$room->contract) continue;
                $meter = $room->meterReadings->first();
                if (!$meter) continue;

                // ข้ามห้องที่บิลชำระแล้วหรือรอการอนุมัติ
                $lockedBill = Bill::where('room_id', $room->id)
                    ->where('billing_month', $billingMonth)
                    ->whereIn('status', ['paid', 'reviewing'])
                    ->exists();
                if ($lockedBill) continue;

                $elecUnits  = $meter->elec_unit  ?? 0;
                $waterUnits = $meter->water_unit ?? 0;
                $elecAmt    = $elecUnits  * ($setting->electric_rate ?? 0);
                $waterAmt   = $waterUnits * ($setting->water_rate    ?? 0);
                $roomRate   = $setting->rent_per_month ?? 0;
                $total      = $roomRate + $elecAmt + $waterAmt;

                $autoBill = Bill::updateOrCreate(
                    ['room_id' => $room->id, 'billing_month' => $billingMonth],
                    [
                        'bill_date'       => now()->toDateString(),
                        'electric_units'  => $elecUnits,
                        'electric_amount' => $elecAmt,
                        'water_units'     => $waterUnits,
                        'water_amount'    => $waterAmt,
                        'room_rate'       => $roomRate,
                        'other_fees'      => 0,
                        'total_price'     => $total,
                        'total_amount'    => $total,
                        'due_date'        => $dueDate,
                        'status'          => 'pending',
                    ]
                );

                // เมื่อสร้างบิลใหม่ ให้รีเซ็ต payment_status ของห้องเป็น ค้างชำระ
                if ($autoBill->wasRecentlyCreated) {
                    $room->update(['payment_status' => 'ค้างชำระ']);
                }

                Notification::notifyTenantByRoom(
                    $room->id, 'bill_created',
                    'บิลค่าเช่าประจำเดือน ' . $monthLabel,
                    'มีบิลค่าเช่าห้อง ' . $room->room_number . ' จำนวน ' . number_format($total, 2) . ' บาท',
                    route('tenant.bills')
                );
                $count++;
            }
        }

        if ($count === 0) {
            return response()->json(['success' => false, 'message' => 'ไม่มีห้องที่มีข้อมูลมิเตอร์ครบถ้วน']);
        }

        return response()->json(['success' => true, 'count' => $count]);
    }

    /**
     * หน้าดูบิล
     */
    public function view(Request $request, $id)
    {
        $selectedMonth = $request->query('month', Carbon::now()->format('Y-m'));

        $room = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }])->findOrFail($id);

        $meter = $room->meterReadings->first();

        // ดึงบิลของห้องนี้ในเดือนที่เลือก
        $bill = Bill::where('room_id', $id)
                    ->where('billing_month', $selectedMonth)
                    ->first();

        // ดึงข้อมูลหอพัก
        $setting = Setting::getInstance();

        return view('rooms.bill_view', compact('room', 'bill', 'meter', 'selectedMonth', 'setting'));
    }

    /**
     * ยืนยันการชำระเงินและสร้างใบเสร็จ
     */
    public function confirmPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'payment_slip' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $bill = Bill::findOrFail($id);

        // อัปโหลดสลิป (ถ้ามี)
        $slipPath = null;
        if ($request->hasFile('payment_slip')) {
            $slipPath = $request->file('payment_slip')->store('payment_slips', 'public');
        }

        // กำหนดวันที่ชำระ
        $paymentDate = $request->payment_date ? Carbon::parse($request->payment_date) : Carbon::now();

        DB::beginTransaction();
        try {
            // สร้างใบเสร็จ
            $receipt = Receipt::create([
                'receipt_number' => Receipt::generateReceiptNumber(),
                'receipt_date' => $paymentDate,
                'amount_paid' => $bill->total_amount,
                'payment_method' => $request->payment_method ?? 'เงินสด',
                'payment_slip' => $slipPath,
                'notes' => $request->notes,
                'bill_id' => $bill->id,
                'received_by' => Auth::id(),
            ]);

            // อัปเดตสถานะบิล
            $bill->update(['status' => 'paid']);

            // อัปเดตสถานะการชำระในห้อง
            $room = $bill->room;
            if ($room) {
                $room->update(['payment_status' => 'ชำระแล้ว']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('confirmPayment error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก กรุณาลองใหม่อีกครั้ง'], 500);
        }

        // Send notification to tenant (outside transaction is fine)
        $room = $bill->fresh()->room;
        Notification::notifyTenantByRoom(
            $bill->room_id,
            'payment_confirmed',
            'ยืนยันการชำระเงินเรียบร้อย',
            'การชำระเงินห้อง ' . ($room->room_number ?? '') . ' จำนวน ' . number_format($bill->total_amount, 2) . ' บาท ได้รับการยืนยันแล้ว',
            route('tenant.receipt', $bill->id)
        );

        return response()->json([
            'success' => true,
            'message' => 'ยืนยันการชำระเงินเรียบร้อย',
            'receipt_number' => $receipt->receipt_number,
        ]);
    }

    /**
     * ดูใบเสร็จ
     */
    public function viewReceipt($id)
    {
        $bill = Bill::with(['room.contract', 'receipt.receiver'])->findOrFail($id);

        if (!$bill->receipt) {
            return redirect()->back()->with('error', 'ไม่พบใบเสร็จสำหรับบิลนี้');
        }

        // ดึงข้อมูลหอพัก
        $setting = Setting::getInstance();

        return view('rooms.receipt_view', compact('bill', 'setting'));
    }
}