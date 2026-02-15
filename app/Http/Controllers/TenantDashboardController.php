<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\MeterReading;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantDashboardController extends Controller
{
    /**
     * Get the current tenant's contract
     */
    private function getTenantContract()
    {
        return Contract::where('user_id', Auth::id())
            ->with(['room.bills.receipt'])
            ->first();
    }

    /**
     * Tenant Dashboard - Main page
     */
    public function index()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $room = $contract->room;
        $setting = Setting::getInstance();

        // Latest unpaid bill
        $latestUnpaidBill = $room->bills()
            ->where('status', '!=', 'paid')
            ->latest('billing_month')
            ->first();

        // Bill history (all bills, latest first)
        $billHistory = $room->bills()
            ->with('receipt')
            ->latest('billing_month')
            ->get();

        // Chart data for dashboard (last 6 months costs breakdown)
        $recentBills = $room->bills()->orderBy('billing_month', 'asc')->take(6)->get();
        $chartLabels = [];
        $chartElectric = [];
        $chartWater = [];
        $chartRoom = [];
        foreach ($recentBills as $b) {
            $chartLabels[] = thaiMonth(\Carbon\Carbon::parse($b->billing_month)->format('m'));
            $chartElectric[] = $b->electric_amount ?? 0;
            $chartWater[] = $b->water_amount ?? 0;
            $chartRoom[] = $b->room_rate ?? 0;
        }

        return view('tenant.dashboard', compact(
            'contract', 'room', 'setting', 'latestUnpaidBill', 'billHistory',
            'chartLabels', 'chartElectric', 'chartWater', 'chartRoom'
        ));
    }

    /**
     * View all bills for tenant
     */
    public function bills(Request $request)
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $query = $contract->room->bills();

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $bills = $query->latest('billing_month')->paginate(50);
        $setting = Setting::getInstance();

        return view('tenant.bills', compact('contract', 'bills', 'setting'));
    }

    /**
     * View single bill detail
     */
    public function viewBill($id)
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        // Make sure the bill belongs to tenant's room
        $bill = $contract->room->bills()->where('id', $id)->with('receipt')->firstOrFail();
        $setting = Setting::getInstance();

        return view('tenant.bill-view', compact('contract', 'bill', 'setting'));
    }

    /**
     * View contract menu page
     */
    public function contract()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $setting = Setting::getInstance();

        return view('tenant.contract', compact('contract', 'setting'));
    }

    /**
     * View contract detail page (full info)
     */
    public function contractDetail()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $setting = Setting::getInstance();

        return view('tenant.contract-detail', compact('contract', 'setting'));
    }

    /**
     * Meter readings page with charts
     */
    public function meters()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $room = $contract->room;
        $setting = Setting::getInstance();

        // Get last 6 months of meter readings
        $meterReadings = MeterReading::where('room_id', $room->id)
            ->orderBy('billing_month', 'asc')
            ->take(6)
            ->get();

        // Prepare chart data
        $chartLabels = [];
        $electricUnits = [];
        $waterUnits = [];
        $electricCosts = [];
        $waterCosts = [];

        foreach ($meterReadings as $reading) {
            $month = \Carbon\Carbon::parse($reading->billing_month);
            $chartLabels[] = $month->format('m') . '/' . $month->format('Y');
            $electricUnits[] = $reading->elec_unit ?? 0;
            $waterUnits[] = $reading->water_unit ?? 0;
            $electricCosts[] = ($reading->elec_unit ?? 0) * ($setting->electric_rate ?? 8);
            $waterCosts[] = ($reading->water_unit ?? 0) * ($setting->water_rate ?? 18);
        }

        // Stats
        $electricStats = [
            'max' => count($electricUnits) ? max($electricUnits) : 0,
            'min' => count($electricUnits) ? min($electricUnits) : 0,
            'avg' => count($electricUnits) ? round(array_sum($electricUnits) / count($electricUnits)) : 0,
        ];

        $waterStats = [
            'max' => count($waterUnits) ? max($waterUnits) : 0,
            'min' => count($waterUnits) ? min($waterUnits) : 0,
            'avg' => count($waterUnits) ? round(array_sum($waterUnits) / count($waterUnits)) : 0,
        ];

        return view('tenant.meters', compact(
            'contract', 'room', 'setting',
            'chartLabels', 'electricUnits', 'waterUnits',
            'electricCosts', 'waterCosts',
            'electricStats', 'waterStats'
        ));
    }

    /**
     * View receipt for a paid bill
     */
    public function receipt($id)
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $bill = $contract->room->bills()->where('id', $id)->with(['receipt.receiver'])->firstOrFail();

        if (!$bill->receipt) {
            abort(404, 'ยังไม่มีใบเสร็จสำหรับบิลนี้');
        }

        $setting = Setting::getInstance();

        return view('tenant.receipt', compact('contract', 'bill', 'setting'));
    }

    /**
     * Tenant profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $contract = $this->getTenantContract();
        $setting = Setting::getInstance();

        return view('tenant.profile', compact('user', 'contract', 'setting'));
    }

    /**
     * Update tenant profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'tenant_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        // Update user email
        if (!empty($validated['email'])) {
            $user->email = $validated['email'];
        }
        $user->save();

        // Update contract tenant info
        $contract = Contract::where('user_id', $user->id)->first();
        if ($contract) {
            if (!empty($validated['tenant_name'])) {
                $contract->tenant_name = $validated['tenant_name'];
            }
            if (isset($validated['phone'])) {
                $contract->phone = $validated['phone'];
            }
            $contract->save();
        }

        return redirect()->route('tenant.profile')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }
}
