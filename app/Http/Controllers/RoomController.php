<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Room;
use App\Models\User;
use App\Models\MeterReading;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        // 1. รับค่าชั้นจาก URL ถ้าไม่มีค่ามาให้ใช้ชั้น 1 เป็นค่าพื้นฐาน
        $currentFloor = $request->query('floor', 1);

        // 2. ดึงข้อมูลสถิติ (นับรวมทุกห้อง ทุกชั้น เพื่อโชว์บนการ์ด 4 ใบ)
        $data = [
            'total_vacant'   => Room::where('status', 'ว่าง')->count(),
            'total_occupied' => Room::where('status', 'ไม่ว่าง')->count(),
            'total_paid'     => Room::where('payment_status', 'ชำระแล้ว')->count(),
            'total_pending'  => Room::where('payment_status', 'ค้างชำระ')->count(),

            // สถิติเฉพาะชั้นที่เลือก (สำหรับ Chart)
            'floor_vacant'   => Room::where('floor', $currentFloor)->where('status', 'ว่าง')->count(),
            'floor_occupied' => Room::where('floor', $currentFloor)->where('status', 'ไม่ว่าง')->count(),

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
        // 1. ตรวจสอบข้อมูลที่ส่งมา (Validation)
        $request->validate([
            'tenant_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'nid'   => 'nullable|string|max:20',
            // ถ้าจะเช็คไฟล์ด้วย ให้เปิดคอมเมนต์บรรทัดล่าง
            // 'contract_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        // 2. ค้นหาห้อง
        $room = Room::findOrFail($id);

        // 3. เตรียมข้อมูลสำหรับ Contract (สัญญา)
        $contractData = [
            'tenant_name' => $request->tenant_name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'nid'         => $request->nid,
            // ถ้าใน DB มีคอลัมน์ contract_duration, check_in_date ให้เพิ่มตรงนี้
            // 'duration' => $request->contract_duration, 
            // 'created_at' => $request->check_in_date, 
        ];

        // 4. จัดการอัปโหลดไฟล์ (ถ้ามี)
        if ($request->hasFile('contract_file')) {
            // เก็บไฟล์ลง folder 'contracts' ใน storage/app/public
            $path = $request->file('contract_file')->store('contracts', 'public');
            $contractData['contract_file'] = $path;
        }

        if ($request->hasFile('idcard_file')) {
            $path = $request->file('idcard_file')->store('idcards', 'public');
            $contractData['idcard_file'] = $path;
        }

        // 5. บันทึกข้อมูล (Update หรือ Create ถ้ายังไม่มี)
        // ใช้ updateOrCreate โดยอิงจาก roomId
        $room->contract()->updateOrCreate(
            ['roomId' => $room->roomId], // เงื่อนไขการหา (ใช้ roomId)
            $contractData // ข้อมูลที่จะบันทึก
        );

        // 6. อัปเดตสถานะห้อง (ถ้ามีการเลือกสถานะมา)
        if ($request->has('tenant_status')) {
            if ($request->tenant_status == 'moving_out') {
                // กรณีแจ้งย้ายออก อาจจะแค่อัปเดตสถานะห้อง หรือทำอย่างอื่นตาม Logic
                // $room->status = 'แจ้งย้ายออก'; 
                // $room->save();
            } else {
                // ปกติถ้ามีคนเช่า สถานะควรเป็น "ไม่ว่าง"
                $room->status = 'ไม่ว่าง';
                $room->save();
            }
        }

        // 7. เด้งกลับไปหน้า Dashboard พร้อมข้อความแจ้งเตือน
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
            'email' => 'nullable|email|max:100',
            'nid' => 'required|string|max:20',
            'username' => 'nullable|string|max:255|unique:users,username',
            'password' => 'nullable|string|min:6',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'contract_duration' => 'required|in:6,12',
            'check_in_date' => 'required|date',
            'contract_date' => 'required|date',
            'tenant_status' => 'nullable|in:active,reserved',
            'initial_electric_meter' => 'nullable|numeric|min:0',
            'initial_water_meter' => 'nullable|numeric|min:0',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'idcard_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $room = Room::findOrFail($id);

        DB::beginTransaction();
        try {
            // Create user account if username provided
            $userId = null;
            if (!empty($request->username) && !empty($request->password)) {
                $user = User::create([
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'email' => $request->email,
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

            // Create contract
            $room->contract()->create([
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
                'contract_file' => $contractFilePath,
                'idcard_file' => $idcardFilePath,
            ]);

            // Update room status
            $room->update([
                'status' => $request->tenant_status == 'reserved' ? 'จอง' : 'ไม่ว่าง',
            ]);

            // Create initial meter readings if provided
            if (!empty($request->initial_electric_meter) || !empty($request->initial_water_meter)) {
                MeterReading::create([
                    'room_id' => $room->id,
                    'electric_reading' => $request->initial_electric_meter ?? 0,
                    'water_reading' => $request->initial_water_meter ?? 0,
                    'reading_date' => $request->check_in_date,
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