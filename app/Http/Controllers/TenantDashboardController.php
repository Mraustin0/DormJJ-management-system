<?php

namespace App\Http\Controllers;

use App\Models\Contract;
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
        $latestBill = $room->bills()->latest('billing_month')->first();
        $unpaidBills = $room->bills()->where('status', '!=', 'paid')->get();
        $totalUnpaid = $unpaidBills->sum('total_amount');

        return view('tenant.dashboard', compact('contract', 'room', 'latestBill', 'unpaidBills', 'totalUnpaid'));
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

        $bills = $query->latest('billing_month')->paginate(10);

        return view('tenant.bills', compact('contract', 'bills'));
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
     * View contract details
     */
    public function contract()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        return view('tenant.contract', compact('contract'));
    }

    /**
     * Tenant profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $contract = $this->getTenantContract();

        return view('tenant.profile', compact('user', 'contract'));
    }

    /**
     * Update tenant profile
     */
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

        return redirect()->route('tenant.profile')->with('success', 'บันทึกข้อมูลโปรไฟล์เรียบร้อยแล้ว');
    }
}
