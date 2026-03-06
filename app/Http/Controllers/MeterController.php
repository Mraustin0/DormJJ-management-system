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
        }, 'allMeterReadings' => function($q) {
            $q->orderBy('billing_month', 'asc');
        }])->orderBy('room_number', 'asc')->get();

        $rooms->each(function ($room) use ($selectedMonth) {
            $currentMonthReading = $room->meterReadings->first();
            
            if ($currentMonthReading) {
                $room->display_water_prev = $currentMonthReading->water_prev;
                $room->display_elec_prev = $currentMonthReading->elec_prev;
            } else {
                // Find the latest meter reading before the selected month
                $previousReading = $room->allMeterReadings
                                        ->filter(function ($reading) use ($selectedMonth) {
                                            return $reading->billing_month < $selectedMonth;
                                        })
                                        ->last();
                
                if ($previousReading) {
                    $room->display_water_prev = $previousReading->water_curr ?? $previousReading->water_prev;
                    $room->display_elec_prev = $previousReading->elec_curr ?? $previousReading->elec_prev;
                } else {
                    // No previous reading, use the very first initial reading (water_prev/elec_prev of the earliest entry)
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
        $floor = $request->input('floor', 1);

        $rooms = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }, 'allMeterReadings' => function($q) {
            $q->orderBy('billing_month', 'asc');
        }])->where('room_number', 'like', $floor . '%')
          ->orderBy('room_number', 'asc')
          ->get();

        $rooms->each(function ($room) use ($selectedMonth) {
            // คำนวณ water_prev จากเดือนก่อนที่มี water_curr บันทึกไว้ (ไม่เชื่อค่าที่เก็บใน record ปัจจุบัน)
            $previousReading = $room->allMeterReadings
                ->filter(fn($r) => $r->billing_month < $selectedMonth && $r->water_curr !== null)
                ->last();
            if ($previousReading) {
                $room->display_water_prev = $previousReading->water_curr;
            } else {
                $firstReading = $room->allMeterReadings->first();
                $room->display_water_prev = $firstReading ? ($firstReading->water_prev ?? 0) : 0;
            }
        });

        // ดึงสถานะบิลของทุกห้องในเดือนที่เลือก (room_id => status)
        $billStatuses = Bill::where('billing_month', $selectedMonth)
            ->pluck('status', 'room_id')
            ->toArray();

        return view('rooms.meters_water', compact('rooms', 'selectedMonth', 'floor', 'isCurrentMonth', 'billStatuses'));
    }

    public function electricIndex(Request $request)
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $selectedMonth = $request->input('month', $currentMonth);
        $isCurrentMonth = $selectedMonth === $currentMonth;
        $floor = $request->input('floor', 1);

        $rooms = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }, 'allMeterReadings' => function($q) {
            $q->orderBy('billing_month', 'asc');
        }])->where('room_number', 'like', $floor . '%')
          ->orderBy('room_number', 'asc')
          ->get();

        $rooms->each(function ($room) use ($selectedMonth) {
            // คำนวณ elec_prev จากเดือนก่อนที่มี elec_curr บันทึกไว้ (ไม่เชื่อค่าที่เก็บใน record ปัจจุบัน)
            $previousReading = $room->allMeterReadings
                ->filter(fn($r) => $r->billing_month < $selectedMonth && $r->elec_curr !== null)
                ->last();
            if ($previousReading) {
                $room->display_elec_prev = $previousReading->elec_curr;
            } else {
                $firstReading = $room->allMeterReadings->first();
                $room->display_elec_prev = $firstReading ? ($firstReading->elec_prev ?? 0) : 0;
            }
        });

        // ดึงสถานะบิลของทุกห้องในเดือนที่เลือก (room_id => status)
        $billStatuses = Bill::where('billing_month', $selectedMonth)
            ->pluck('status', 'room_id')
            ->toArray();

        return view('rooms.meters_electric', compact('rooms', 'selectedMonth', 'floor', 'isCurrentMonth', 'billStatuses'));
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
