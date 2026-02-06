<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeterReading;
use App\Models\Room;
use App\Models\Bill;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
            'paid' => 'ชำระแล้ว',
            'overdue' => 'ค้างชำระ',
        ];

        return view('rooms.bills', compact('bills', 'months', 'selectedMonth', 'statuses', 'selectedStatus'));
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

        return view('rooms.bills_create', compact('rooms', 'currentFloor', 'selectedMonth', 'search'));
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

        return view('rooms.bill_form', compact('room', 'selectedMonth'));
    }

    /**
     * บันทึกบิล
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'billing_month' => 'required',
            'total_amount' => 'required|numeric',
        ]);

        // สร้างหรืออัปเดตบิล
        Bill::updateOrCreate(
            [
                'room_id' => $request->room_id,
                'billing_month' => $request->billing_month,
            ],
            [
                'water_units' => $request->water_units ?? 0,
                'water_amount' => $request->water_amount ?? 0,
                'electric_units' => $request->electric_units ?? 0,
                'electric_amount' => $request->electric_amount ?? 0,
                'room_rate' => $request->room_rate ?? 0,
                'other_fees' => $request->other_fees ?? 0,
                'total_amount' => $request->total_amount,
                'status' => 'pending',
            ]
        );

        return response()->json(['success' => true, 'message' => 'บันทึกบิลเรียบร้อยแล้ว']);
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

        return view('rooms.bill_view', compact('room', 'bill', 'meter', 'selectedMonth'));
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

        return view('rooms.receipt_view', compact('bill'));
    }
}