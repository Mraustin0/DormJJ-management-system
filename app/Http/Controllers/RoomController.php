<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Notification;
use App\Models\Room;
use App\Models\User;
use App\Models\MeterReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        // ── Auto-update: ห้องที่ถึงวันเข้าพักแล้ว → เปลี่ยนเป็น ไม่ว่าง ──────────
        $today = \Carbon\Carbon::today()->toDateString();
        $dueRooms = Room::whereIn('status', ['จอง', 'รอเข้าพัก'])
            ->whereHas('contract', function ($q) use ($today) {
                $q->whereIn('status', ['active', 'ending'])
                  ->whereDate('check_in_date', '<=', $today);
            })->get();
        foreach ($dueRooms as $r) {
            $r->update(['status' => 'ไม่ว่าง']);
        }

        // 1. รับค่าชั้นจาก URL ถ้าไม่มีค่ามาให้ใช้ชั้น 1 เป็นค่าพื้นฐาน
        $currentFloor = $request->query('floor', 1);

        // 2. ดึงข้อมูลสถิติ (นับรวมทุกห้อง ทุกชั้น เพื่อโชว์บนการ์ด 4 ใบ)
        $data = [
            'total_vacant'   => Room::where('status', 'ว่าง')->count(),
            'total_occupied' => Room::where('status', 'ไม่ว่าง')->count(),
            'total_waiting'  => Room::whereIn('status', ['รอเข้าพัก', 'จอง'])->count(),
            'total_pending'  => Room::where('payment_status', 'ค้างชำระ')->count(),

            // สถิติเฉพาะชั้นที่เลือก (สำหรับ Chart)
            'floor_vacant'   => Room::where('floor', $currentFloor)->where('status', 'ว่าง')->count(),
            'floor_occupied' => Room::where('floor', $currentFloor)->where('status', 'ไม่ว่าง')->count(),
            'floor_waiting'  => Room::where('floor', $currentFloor)->whereIn('status', ['รอเข้าพัก', 'จอง'])->count(),

            // 3. ดึงเฉพาะห้องในชั้นที่เลือกมาแสดงผล
            'rooms'          => Room::with('contract')->where('floor', $currentFloor)->orderBy('room_number', 'asc')->get(),
            'currentFloor'   => $currentFloor,
        ];

        return view('rooms.index', $data);
    }


    public function moveOutForm($id)
    {
        // ดึงข้อมูลห้อง + สัญญาเช่า (ผู้เช่า)
        $room = Room::with('contract')->findOrFail($id);

        // ส่งข้อมูลไปที่หน้า view moveout
        return view('rooms.moveout', compact('room'));
    }

    public function edit($id)
    {
        // ดึงข้อมูลห้องตาม ID
        $room = Room::findOrFail($id);

        // ส่งข้อมูลห้องไปที่หน้า view edit
        return view('rooms.edit', compact('room'));
    }


    // ... (ต่อจาก function edit)

    public function update(Request $request, $id)
    {
        // 1. Validation
        $request->validate([
            'tenant_name'       => 'required|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email',
            'nid'               => 'nullable|string|max:20',
            'check_in_date'     => 'nullable|date',
            'contract_duration' => 'nullable|in:6,12,24',
            'contract_file'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'idcard_file'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // 2. ค้นหาห้อง
        $room = Room::findOrFail($id);

        // 3. เตรียมข้อมูลสำหรับ Contract
        $contractData = [
            'tenant_name' => $request->tenant_name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'nid'         => $request->nid,
        ];

        // เพิ่ม check_in_date และคำนวณ end_date ใหม่ถ้ามีการเปลี่ยน
        if ($request->filled('check_in_date') || $request->filled('contract_duration')) {
            $existingContract = $room->contract;
            $checkIn  = Carbon::parse($request->check_in_date ?? $existingContract?->check_in_date);
            $duration = (int) ($request->contract_duration ?? $existingContract?->contract_duration ?? 12);

            $contractData['check_in_date']     = $checkIn->toDateString();
            $contractData['start_date']        = $checkIn->toDateString();
            $contractData['contract_duration'] = $duration;
            $contractData['end_date']          = $checkIn->copy()->addMonths($duration)->toDateString();
        }

        // 4. จัดการอัปโหลดไฟล์
        if ($request->hasFile('contract_file')) {
            $contractData['contract_file'] = $request->file('contract_file')->store('contracts', 'public');
        }
        if ($request->hasFile('idcard_file')) {
            $contractData['idcard_file'] = $request->file('idcard_file')->store('idcards', 'public');
        }

        // 5. บันทึก Contract (ใช้ contracts() hasMany เพื่อ updateOrCreate เฉพาะสัญญา active/ending)
        $activeContract = $room->contracts()
            ->whereIn('status', ['active', 'ending'])
            ->latest('start_date')
            ->first();

        if ($activeContract) {
            $activeContract->update($contractData);
        } else {
            $room->contracts()->updateOrCreate(
                ['room_id' => $room->id],
                $contractData
            );
        }

        // 6. อัปเดตสถานะห้อง
        if ($request->has('tenant_status')) {
            if ($request->tenant_status === 'moving_out') {
                $room->update(['status' => 'แจ้งย้ายออก']);
            } elseif ($request->tenant_status === 'active') {
                // เช็ค check_in_date: ถ้ายังไม่ถึง → รอเข้าพัก, ถ้าถึงแล้ว → ไม่ว่าง
                $ciDate = $request->filled('check_in_date')
                    ? Carbon::parse($request->check_in_date)
                    : ($room->contract?->check_in_date ? Carbon::parse($room->contract->check_in_date) : null);
                $newStatus = ($ciDate && $ciDate->isAfter(Carbon::today())) ? 'รอเข้าพัก' : 'ไม่ว่าง';
                $room->update(['status' => $newStatus]);
            } elseif ($request->tenant_status === 'moved_out') {
                $room->update(['status' => 'ว่าง', 'payment_status' => null]);

                // ปิดสัญญา active/ending ที่ยังเปิดอยู่
                $activeContract = $room->contracts()
                    ->whereIn('status', ['active', 'ending'])
                    ->latest('start_date')
                    ->first();
                if ($activeContract) {
                    $activeContract->update([
                        'status'   => 'expired',
                        'end_date' => $activeContract->end_date ?? now()->toDateString(),
                    ]);
                }
            }
        }

        // 7. Redirect
        return redirect()->route('rooms.index')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    public function createContract($id)
    {
        $room = Room::findOrFail($id);

        return view('rooms.create_contract', compact('room'));
    }

    public function viewContract($id)
    {
        $room = Room::with('contract')->findOrFail($id);
        $contract = $room->contract;

        if (!$contract) {
            return redirect()->route('rooms.index')->with('error', 'ไม่พบสัญญาเช่าสำหรับห้องนี้');
        }

        $setting = \App\Models\Setting::getInstance();

        return view('rooms.view_contract', compact('room', 'contract', 'setting'));
    }

    public function storeContract(Request $request, $id)
    {
        $request->validate([
            'tenant_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100|unique:users,email',
            'nid' => 'required|string|max:20',
            'username' => 'nullable|string|max:255|unique:users,username',
            'password' => 'nullable|string|min:6',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'contract_duration' => 'required|in:6,12',
            'check_in_date' => 'required|date',
            'contract_date' => 'required|date',
            'initial_electric_meter' => 'nullable|numeric|min:0',
            'initial_water_meter' => 'nullable|numeric|min:0',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'idcard_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $room = Room::findOrFail($id);

        // ─── วิธีที่ 1: ตรวจสอบสัญญาทับซ้อน ───────────────────────────────────
        $startDate = Carbon::parse($request->check_in_date);
        $endDate   = $startDate->copy()->addMonths((int) $request->contract_duration);

        $overlap = Contract::where('room_id', $room->id)
            ->whereIn('status', ['active', 'ending'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereNotNull('start_date')
                  ->whereNotNull('end_date')
                  ->where('start_date', '<=', $endDate)
                  ->where('end_date',   '>=', $startDate);
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withInput()
                ->withErrors(['check_in_date' => 'มีสัญญาเช่าอยู่แล้วในช่วงเวลานี้ (' .
                    $startDate->format('d/m/Y') . ' – ' . $endDate->format('d/m/Y') .
                    ') กรุณาตรวจสอบสัญญาเดิมก่อน']);
        }
        // ─────────────────────────────────────────────────────────────────────

        DB::beginTransaction();
        try {
            // Create user account if username provided
            $userId = null;
            if (!empty($request->username) && !empty($request->password)) {
                $user = User::create([
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'email' => !empty($request->email) ? $request->email : null,
                    'tenant_role' => true,
                ]);
                $userId = $user->id;
            }

            // Handle file uploads
            $contractFilePath = null;
            $idcardFilePath = null;

            if ($request->hasFile('contract_file')) {
                $contractFilePath = $request->file('contract_file')->store('contracts', 'public');
            }
            if ($request->hasFile('idcard_file')) {
                $idcardFilePath = $request->file('idcard_file')->store('idcards', 'public');
            }

            // ─── วิธีที่ 2: สร้างสัญญาพร้อม status + start/end date ──────────
            $room->contracts()->create([
                'tenant_name' => $request->tenant_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'nid' => $request->nid,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'user_id' => $userId,
                'contract_duration' => $request->contract_duration,
                'check_in_date' => $request->check_in_date,
                'contract_date' => $request->contract_date,
                'start_date'    => $startDate,
                'end_date'      => $endDate,
                'status'        => 'active',
                'contract_file' => $contractFilePath,
                'idcard_file' => $idcardFilePath,
            ]);
            // ─────────────────────────────────────────────────────────────────

            // Update room status: ถ้า check_in_date ยังไม่ถึง → รอเข้าพัก, ถ้าถึงแล้ว → ไม่ว่าง
            $newRoomStatus = $startDate->isAfter(Carbon::today()) ? 'รอเข้าพัก' : 'ไม่ว่าง';
            $room->update(['status' => $newRoomStatus]);

            // Create initial meter readings if provided
            if (!empty($request->initial_electric_meter) || !empty($request->initial_water_meter)) {
                $initElec  = (float) ($request->initial_electric_meter ?? 0);
                $initWater = (float) ($request->initial_water_meter ?? 0);
                MeterReading::create([
                    'room_id'       => $room->id,
                    'billing_month' => $startDate->format('Y-m'),
                    'elec_prev'     => $initElec,
                    'elec_curr'     => $initElec,
                    'elec_unit'     => 0,
                    'water_prev'    => $initWater,
                    'water_curr'    => $initWater,
                    'water_unit'    => 0,
                ]);
            }

            // Send notification to tenant
            if ($userId) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'contract_created',
                    'title' => 'สัญญาเช่าห้อง ' . $room->room_number,
                    'message' => 'สัญญาเช่าของคุณถูกสร้างเรียบร้อยแล้ว ยินดีต้อนรับเข้าสู่หอพัก',
                    'link' => route('tenant.contract.detail'),
                ]);
            }

            DB::commit();
            return redirect()->route('rooms.index')->with('success', 'เพิ่มผู้เช่าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Room store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง'])->withInput();
        }
    }



}