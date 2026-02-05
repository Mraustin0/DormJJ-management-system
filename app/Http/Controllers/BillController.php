<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeterReading;
use Carbon\Carbon;

class BillController extends Controller
{
    public function index(Request $request)
    {
        // 1. สร้าง Query เตรียมดึงข้อมูล (พร้อมข้อมูลห้องและผู้เช่า)
        $query = MeterReading::with(['room.contract']);

        // 2. กรองข้อมูล: ตามเดือน (ถ้ามีการเลือก)
        $selectedMonth = $request->input('month');
        if ($selectedMonth && $selectedMonth != 'all') {
            $query->where('billing_month', $selectedMonth);
        }

        // 3. กรองข้อมูล: ค้นหาห้อง (Search)
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('room', function($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%");
            });
        }

        // 4. ดึงข้อมูลและแบ่งหน้า (Pagination) ทีละ 10 รายการ
        $bills = $query->latest('billing_month') // เรียงเดือนล่าสุดขึ้นก่อน
                       ->orderBy('room_id')      // เรียงตามห้อง
                       ->paginate(10)            // ✨ ตัดหน้าทีละ 10
                       ->appends($request->all()); // คงค่า Filter ไว้ตอนกดเปลี่ยนหน้า

        // 5. สร้างตัวเลือกเดือนย้อนหลัง 12 เดือน สำหรับ Dropdown
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            // เก็บค่าเป็น 'Y-m' (เช่น 2026-02) ไว้ใช้ Filter
            $months[$date->format('Y-m')] = $date->translatedFormat('F Y'); 
        }

        return view('rooms.bills', compact('bills', 'months', 'selectedMonth'));
    }
}