<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\MeterReading;
use Carbon\Carbon;

class MeterController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));

        $rooms = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }])->orderBy('room_number', 'asc')->get();

        return view('rooms.meters', compact('rooms', 'selectedMonth'));
    }

    public function waterIndex(Request $request)
    {
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $floor = $request->input('floor', 1); // default ชั้น 1 = 10 ห้อง

        $rooms = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }])->where('room_number', 'like', $floor . '%')
          ->orderBy('room_number', 'asc')
          ->get();

        return view('rooms.meters_water', compact('rooms', 'selectedMonth', 'floor'));
    }

    public function electricIndex(Request $request)
    {
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $floor = $request->input('floor', 1); // default ชั้น 1 = 10 ห้อง

        $rooms = Room::with(['contract', 'meterReadings' => function($q) use ($selectedMonth) {
            $q->where('billing_month', $selectedMonth);
        }])->where('room_number', 'like', $floor . '%')
          ->orderBy('room_number', 'asc')
          ->get();

        return view('rooms.meters_electric', compact('rooms', 'selectedMonth', 'floor'));
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
