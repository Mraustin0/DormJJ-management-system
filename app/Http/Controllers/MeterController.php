<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\MeterReading;
use App\Models\Bill;
use Carbon\Carbon;

class MeterController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));

        $rooms = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }, 'allMeterReadings'])->orderBy('room_number', 'asc')->get();

        $rooms->each(function ($room) use ($selectedMonth) {
            $currentMonthReading = $room->meterReadings->first();

            if ($currentMonthReading) {
                $room->display_water_prev = $currentMonthReading->water_prev;
                $room->display_elec_prev = $currentMonthReading->elec_prev;
            } else {
                // มิเตอร์เป็นกายภาพ เลขต่อเนื่องไม่ว่าจะเปลี่ยนผู้เช่า → ดึง reading ล่าสุดก่อนเดือนนี้
                $previousReading = $room->allMeterReadings
                    ->filter(function ($reading) use ($selectedMonth) {
                        return $reading->billing_month < $selectedMonth;
                    })
                    ->last();

                if ($previousReading) {
                    $room->display_water_prev = $previousReading->water_curr ?? $previousReading->water_prev;
                    $room->display_elec_prev = $previousReading->elec_curr ?? $previousReading->elec_prev;
                } else {
                    // ยังไม่เคยบันทึกมาก่อนเลย → ใช้ water_prev ของ record แรกสุด
                    $firstReading = $room->allMeterReadings->first();
                    $room->display_water_prev = $firstReading ? ($firstReading->water_prev ?? 0) : 0;
                    $room->display_elec_prev = $firstReading ? ($firstReading->elec_prev ?? 0) : 0;
                }
            }
        });

        return view('rooms.meters', compact('rooms', 'selectedMonth'));
    }

    public function waterIndex(Request $request)
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $selectedMonth = $request->input('month', $currentMonth);
        $isCurrentMonth = $selectedMonth === $currentMonth;
        $isPastMonth = $selectedMonth < $currentMonth; // เฉพาะอดีตเท่านั้นที่ lock
        $floor = $request->input('floor', ''); // '' = ทุกชั้น

        $roomQuery = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }, 'allMeterReadings']);

        if ($floor !== '') {
            $roomQuery->where('floor', (int)$floor);
        }

        $rooms = $roomQuery->orderBy('room_number', 'asc')->get();

        $rooms->each(function ($room) use ($selectedMonth) {
            $currentMonthReading = $room->meterReadings->first();

            // 1. ถ้าเดือนนี้มี water_prev แล้ว → ใช้ค่าที่ admin บันทึกไว้
            if ($currentMonthReading && $currentMonthReading->water_prev !== null) {
                $room->display_water_prev = $currentMonthReading->water_prev;
                return;
            }

            // 2. หา water_curr ล่าสุดจากเดือนก่อน (มิเตอร์ต่อเนื่อง)
            $previousReading = $room->allMeterReadings
                ->filter(fn($r) => $r->billing_month < $selectedMonth && $r->water_curr !== null)
                ->last();

            if ($previousReading) {
                // ถ้า water_prev ของ reading นั้นถูกบันทึกผิด (=0) ให้คำนวณ water_curr จริงจาก init_prev + unit
                if (($previousReading->water_prev ?? 0) > 0) {
                    $room->display_water_prev = $previousReading->water_curr;
                } else {
                    $initRecord = $room->allMeterReadings
                        ->filter(fn($r) => ($r->water_prev ?? 0) > 0)
                        ->first();
                    $room->display_water_prev = $initRecord
                        ? ($initRecord->water_prev + ($previousReading->water_unit ?? 0))
                        : $previousReading->water_curr;
                }
            } else {
                // 3. ยังไม่เคยบันทึกน้ำเลย → ใช้ water_prev ของ record แรก (ค่าเริ่มต้น)
                $firstReading = $room->allMeterReadings->first();
                $room->display_water_prev = $firstReading ? ($firstReading->water_prev ?? 0) : 0;
            }
        });

        // ดึงสถานะบิลของทุกห้องในเดือนที่เลือก (room_id => status)
        $billStatuses = Bill::where('billing_month', $selectedMonth)
            ->pluck('status', 'room_id')
            ->toArray();

        return view('rooms.meters_water', compact('rooms', 'selectedMonth', 'floor', 'isCurrentMonth', 'isPastMonth', 'billStatuses'));
    }

    public function electricIndex(Request $request)
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $selectedMonth = $request->input('month', $currentMonth);
        $isCurrentMonth = $selectedMonth === $currentMonth;
        $isPastMonth = $selectedMonth < $currentMonth; // เฉพาะอดีตเท่านั้นที่ lock
        $floor = $request->input('floor', ''); // '' = ทุกชั้น

        $roomQuery = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }, 'allMeterReadings']);

        if ($floor !== '') {
            $roomQuery->where('floor', (int)$floor);
        }

        $rooms = $roomQuery->orderBy('room_number', 'asc')->get();

        $rooms->each(function ($room) use ($selectedMonth) {
            $currentMonthReading = $room->meterReadings->first();

            // 1. เดือนนี้มี elec_prev ที่มีค่าจริง (> 0) → ข้อมูลถูกต้องแล้ว ใช้เลย
            if ($currentMonthReading && ($currentMonthReading->elec_prev ?? 0) > 0) {
                $room->display_elec_prev = $currentMonthReading->elec_prev;
                $room->display_elec_curr = $currentMonthReading->elec_curr;
                return;
            }

            // 2. หา elec_curr ล่าสุดจากเดือนก่อน (มิเตอร์ต่อเนื่อง)
            $previousReading = $room->allMeterReadings
                ->filter(fn($r) => $r->billing_month < $selectedMonth && $r->elec_curr !== null)
                ->last();

            if ($previousReading) {
                // ถ้า elec_prev ของ reading นั้นถูกบันทึกผิด (=0) ให้คำนวณ elec_curr จริงจาก init_prev + unit
                if (($previousReading->elec_prev ?? 0) > 0) {
                    $truePrevCurr = $previousReading->elec_curr;
                } else {
                    $initRecord = $room->allMeterReadings
                        ->filter(fn($r) => ($r->elec_prev ?? 0) > 0)
                        ->first();
                    $truePrevCurr = $initRecord
                        ? ($initRecord->elec_prev + ($previousReading->elec_unit ?? 0))
                        : $previousReading->elec_curr;
                }
                $room->display_elec_prev = $truePrevCurr;
                if ($currentMonthReading && $currentMonthReading->elec_unit !== null) {
                    $room->display_elec_curr = $truePrevCurr + $currentMonthReading->elec_unit;
                } else {
                    $room->display_elec_curr = $currentMonthReading?->elec_curr;
                }
                return;
            }

            // 3. ไม่มีประวัติไฟเลย → ดึงจาก init record (record ที่มี elec_prev > 0 ไม่ว่าเดือนไหน)
            //    init record ถูกสร้างตอนบันทึกสัญญา เก็บค่ามิเตอร์เริ่มต้นไว้
            $initRecord = $room->allMeterReadings
                ->filter(fn($r) => ($r->elec_prev ?? 0) > 0)
                ->first(); // first = เก่าที่สุด = init record
            $room->display_elec_prev = $initRecord ? $initRecord->elec_prev : 0;

            // คำนวณ display_elec_curr = init_prev + elec_unit ที่บันทึกไว้ (แก้ค่าที่เคยกรอกผิดตอน prev=0)
            if ($currentMonthReading && $currentMonthReading->elec_unit !== null && $room->display_elec_prev > 0) {
                $room->display_elec_curr = $room->display_elec_prev + $currentMonthReading->elec_unit;
            } else {
                $room->display_elec_curr = $currentMonthReading?->elec_curr;
            }
        });

        // ดึงสถานะบิลของทุกห้องในเดือนที่เลือก (room_id => status)
        $billStatuses = Bill::where('billing_month', $selectedMonth)
            ->pluck('status', 'room_id')
            ->toArray();

        return view('rooms.meters_electric', compact('rooms', 'selectedMonth', 'floor', 'isCurrentMonth', 'isPastMonth', 'billStatuses'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'billing_month' => 'required|date_format:Y-m',
            'water_prev' => 'required|integer|min:0',
            'water_curr' => 'required|integer|min:0|gte:water_prev',
            'elec_prev' => 'required|integer|min:0',
            'elec_curr' => 'required|integer|min:0|gte:elec_prev',
            'status' => 'required|in:pending,paid,overdue',
        ]);

        MeterReading::updateOrCreate(
            [
                'room_id' => $request->room_id,
                'billing_month' => $request->billing_month
            ],
            [
                'water_prev' => $request->water_prev,
                'water_curr' => $request->water_curr,
                'water_unit' => $request->water_curr - $request->water_prev,

                'elec_prev' => $request->elec_prev,
                'elec_curr' => $request->elec_curr,
                'elec_unit' => $request->elec_curr - $request->elec_prev,

                'status' => $request->status
            ]
        );

        return response()->json(['success' => true, 'message' => 'บันทึกข้อมูลเรียบร้อย']);
    }

    public function updateWater(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'billing_month' => 'required|date_format:Y-m',
            'water_prev' => 'required|integer|min:0',
            'water_curr' => 'required|integer|min:0|gte:water_prev',
        ]);

        MeterReading::updateOrCreate(
            [
                'room_id' => $request->room_id,
                'billing_month' => $request->billing_month
            ],
            [
                'water_prev' => $request->water_prev,
                'water_curr' => $request->water_curr,
                'water_unit' => $request->water_curr - $request->water_prev,
            ]
        );

        return response()->json(['success' => true, 'message' => 'บันทึกค่าน้ำเรียบร้อย']);
    }

    public function updateElectric(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'billing_month' => 'required|date_format:Y-m',
            'elec_prev' => 'required|integer|min:0',
            'elec_curr' => 'required|integer|min:0|gte:elec_prev',
        ]);

        MeterReading::updateOrCreate(
            [
                'room_id' => $request->room_id,
                'billing_month' => $request->billing_month
            ],
            [
                'elec_prev' => $request->elec_prev,
                'elec_curr' => $request->elec_curr,
                'elec_unit' => $request->elec_curr - $request->elec_prev,
            ]
        );

        return response()->json(['success' => true, 'message' => 'บันทึกค่าไฟเรียบร้อย']);
    }
}
