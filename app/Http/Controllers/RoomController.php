<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room; 

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
            
            // 3. ดึงเฉพาะห้องในชั้นที่เลือกมาแสดงผล
            'rooms'          => Room::where('floor', $currentFloor)->orderBy('room_number', 'asc')->get(),
            'currentFloor'   => $currentFloor
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

    public function storeContract(Request $request, $id)
    {
        // 1. Validation (เพิ่มให้ครบตามฟอร์ม)
        $request->validate([
            'tenant_name'     => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'nid'             => 'required|string|max:20',
            'check_in_date'   => 'required|date',
            'contract_date'   => 'nullable|date',
            'deposit'         => 'nullable|numeric',
            'contract_duration' => 'nullable', 
            // 'contract_file' => 'nullable|file|mimes:pdf,jpg,png|max:10240', // แนะนำให้เปิดเช็คไฟล์
        ]);

        $room = Room::findOrFail($id);

        // 2. เตรียมข้อมูลบันทึก
        $contractData = [
            'tenant_name'   => $request->tenant_name,
            'phone'         => $request->phone,
            'nid'           => $request->nid,
            'email'         => $request->email,
            
            // ✅ เปิดใช้งานส่วนนี้ (เพื่อให้ข้อมูลจากฟอร์มถูกบันทึก)
            // ต้องมั่นใจว่าในตาราง contracts มีชื่อคอลัมน์ตามด้านซ้ายมือนะครับ
            'deposit'       => $request->deposit,             // ค่ามัดจำ
            'duration'      => $request->contract_duration,   // ระยะสัญญา (ใน DB ชื่อ duration หรือเปล่า?)
            'contract_date' => $request->contract_date,       // วันทำสัญญา
            'check_in_date' => $request->check_in_date,       // วันเข้าพัก (แนะนำให้แยกคอลัมน์ ไม่ควรทับ created_at)
            
            // ถ้าอยาก override created_at จริงๆ ให้ใช้บรรทัดนี้ แต่ไม่แนะนำ
            // 'created_at' => $request->check_in_date, 
            
            'status'        => $request->tenant_status ?? 'active', // สถานะสัญญา
        ];

        // 3. อัปโหลดไฟล์
        if ($request->hasFile('contract_file')) {
            $contractData['contract_file'] = $request->file('contract_file')->store('contracts', 'public');
        }
        if ($request->hasFile('idcard_file')) {
            $contractData['idcard_file'] = $request->file('idcard_file')->store('idcards', 'public');
        }

        // 4. บันทึกข้อมูลลงตาราง contracts
        // (ระวัง: ถ้าตาราง contracts ไม่มีคอลัมน์ deposit, duration ฯลฯ จะเกิด Error)
        $room->contract()->create($contractData);

        // 5. อัปเดตสถานะห้อง
        $room->status = 'ไม่ว่าง';
        $room->save();

        return redirect()->route('rooms.index')->with('success', 'เพิ่มผู้เช่าเรียบร้อยแล้ว');
    }



}