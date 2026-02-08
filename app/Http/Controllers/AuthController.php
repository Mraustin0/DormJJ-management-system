<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Room;
use App\Models\Contract;

class AuthController extends Controller
{
    // 1. หน้าแสดงฟอร์ม Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. ระบบ Login (แก้ไขให้ใช้ Username) ✅
    public function login(Request $request)
    {
        // รับค่า username และ password
        $credentials = $request->validate([
            'username' => ['required'], // เปลี่ยนจาก email เป็น username
            'password' => ['required'],
        ]);

        // ตรวจสอบกับ Database
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('rooms.index');
        }

        // ถ้า Login ไม่ผ่าน
        return back()->withErrors([
            'username' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง', // แจ้งเตือนที่ช่อง username
        ])->onlyInput('username');
    }

    // 3. ระบบ Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }

    // 4. หน้า Dashboard (เหมือนเดิม)
    public function index(Request $request)
    {
        $currentFloor = $request->get('floor', 1);

        // ข้อมูลรวมทั้งหมด
        $total_vacant   = Room::where('status', 'ว่าง')->count();
        $total_occupied = Room::where('status', 'ไม่ว่าง')->count();
        $total_paid     = Room::where('payment_status', 'ชำระแล้ว')->count();
        $total_pending  = Room::where('payment_status', 'ค้างชำระ')->count();

        // ข้อมูลตามชั้นที่เลือก (สำหรับ Chart)
        $floor_vacant   = Room::where('floor', $currentFloor)->where('status', 'ว่าง')->count();
        $floor_occupied = Room::where('floor', $currentFloor)->where('status', 'ไม่ว่าง')->count();

        $rooms = Room::with('contract')
                     ->where('floor', $currentFloor)
                     ->orderBy('room_number', 'asc')
                     ->get();

        return view('rooms.index', compact(
            'currentFloor', 'rooms',
            'total_vacant', 'total_occupied', 'total_paid', 'total_pending',
            'floor_vacant', 'floor_occupied'
        ));
    }

    // ฟังก์ชันสำหรับหน้า "ข้อมูลการเข้าพัก"
    public function accommodation()
{
    // ดึงข้อมูลสัญญาเช่า + ข้อมูลห้องพัก (Join ตาราง)
    // เรียงตามวันที่ล่าสุด และแบ่งหน้าทีละ 10 รายการ
    $contracts = Contract::with('room')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10); 

    return view('rooms.accommodation', compact('contracts'));
}

    // หน้า Profile
    public function profile()
    {
        $user = Auth::user();
        return view('rooms.profile', compact('user'));
    }

    // อัปเดต Profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->username = $validated['username'];
        if (!empty($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->save();

        return redirect()->route('profile.index')->with('success', 'บันทึกข้อมูลโปรไฟล์เรียบร้อยแล้ว');
    }

}