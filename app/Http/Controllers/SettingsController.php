<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $setting = Setting::getInstance();
        return view('rooms.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::getInstance();

        $validated = $request->validate([
            'rent_per_month' => 'required|numeric|min:0',
            'water_rate' => 'required|numeric|min:0',
            'electric_rate' => 'required|numeric|min:0',
            'late_fee_per_day' => 'required|numeric|min:0',
            'payment_due_day' => 'required|string',
            'apartment_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $setting->update($validated);

        return redirect()->route('settings.index')->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }
}
