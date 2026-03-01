<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\MeterReading;
use App\Models\MoveoutRequest;
use App\Models\Notification;
use App\Models\Repair;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantDashboardController extends Controller
{
    /**
     * Get the current tenant's contract
     */
    private function getTenantContract()
    {
        return Contract::where('user_id', Auth::id())
            ->with('room')
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

        // Build full address string
        $addressParts = array_filter([
            $setting->address,
            $setting->subdistrict ? 'ตำบล' . $setting->subdistrict : null,
            $setting->district ? 'อำเภอ' . $setting->district : null,
            $setting->province ? 'จังหวัด' . $setting->province : null,
            $setting->postal_code,
        ]);
        $fullAddress = implode(' ', $addressParts) ?: '-';

        // Bills summary for dashboard
        $recentBills = Bill::where('room_id', $room->id)
            ->latest('billing_month')
            ->take(3)
            ->with('receipt')
            ->get();

        $currentBill = $recentBills->first();

        $unpaidCount = Bill::where('room_id', $room->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->count();

        // Meter chart data (last 6 months)
        $meterReadings = MeterReading::where('room_id', $room->id)
            ->orderBy('billing_month', 'asc')
            ->take(6)
            ->get();

        $chartLabels  = $meterReadings->map(fn($r) => \Carbon\Carbon::parse($r->billing_month . '-01')->format('m/Y'))->toArray();
        $electricData = $meterReadings->pluck('elec_unit')->toArray();
        $waterData    = $meterReadings->pluck('water_unit')->toArray();

        return view('tenant.dashboard', compact(
            'contract', 'room', 'setting', 'fullAddress',
            'recentBills', 'currentBill', 'unpaidCount',
            'chartLabels', 'electricData', 'waterData'
        ));
    }

    /**
     * Room Info page - apartment details, rates, map
     */
    public function roomInfo()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $setting = Setting::getInstance();

        $addressParts = array_filter([
            $setting->address,
            $setting->subdistrict ? 'ตำบล' . $setting->subdistrict : null,
            $setting->district ? 'อำเภอ' . $setting->district : null,
            $setting->province ? 'จังหวัด' . $setting->province : null,
            $setting->postal_code,
        ]);
        $fullAddress = implode(' ', $addressParts) ?: '-';

        return view('tenant.room-info', compact('contract', 'setting', 'fullAddress'));
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
     * Upload payment slip for a bill
     */
    public function uploadSlip(Request $request, $id)
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        // Ensure bill belongs to tenant's room
        $bill = $contract->room->bills()->where('id', $id)->firstOrFail();

        $request->validate([
            'payment_slip' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'payment_slip.required' => 'กรุณาเลือกไฟล์สลิป',
            'payment_slip.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'payment_slip.mimes' => 'รองรับไฟล์ JPG, PNG, WEBP เท่านั้น',
            'payment_slip.max' => 'ไฟล์ต้องมีขนาดไม่เกิน 5MB',
        ]);

        try {
            // Delete old slip if exists
            if ($bill->payment_slip) {
                Storage::disk('public')->delete($bill->payment_slip);
            }

            // Store new slip
            $path = $request->file('payment_slip')->store('payment_slips', 'public');

            if (!$path) {
                return redirect()->route('tenant.bills.view', $id)
                    ->withErrors(['payment_slip' => 'ไม่สามารถอัปโหลดไฟล์ได้ กรุณาลองใหม่']);
            }

            $bill->update([
                'payment_slip' => $path,
                'status' => 'reviewing',
            ]);

            return redirect()->route('tenant.bills.view', $id)->with('slip_success', 'อัปโหลดสลิปเรียบร้อยแล้ว รอแอดมินตรวจสอบ');
        } catch (\Exception $e) {
            \Log::error('Slip upload error: ' . $e->getMessage());
            return redirect()->route('tenant.bills.view', $id)
                ->withErrors(['payment_slip' => 'เกิดข้อผิดพลาดในการอัปโหลด กรุณาลองใหม่']);
        }
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
     * Contract history page (dynamic data from system)
     */
    public function contractHistory()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $setting = Setting::getInstance();

        // Build dynamic history from real contract data
        $histories = [];

        // 1. Contract created (from contract_date or created_at)
        $contractCreatedAt = $contract->contract_date
            ? \Carbon\Carbon::parse($contract->contract_date)
            : $contract->created_at;

        $histories[] = [
            'type' => 'created',
            'date' => $contractCreatedAt->format('j') . ' ' . thaiMonth($contractCreatedAt->format('m')) . ' ' . ($contractCreatedAt->format('Y')),
            'time' => $contractCreatedAt->format('H : i'),
            'by' => 'Admin',
        ];

        // 2. Contract file uploaded (if exists)
        if ($contract->contract_file) {
            $fileDate = $contractCreatedAt->copy()->addMinutes(5);
            $histories[] = [
                'type' => 'sent',
                'date' => $fileDate->format('j') . ' ' . thaiMonth($fileDate->format('m')) . ' ' . ($fileDate->format('Y')),
                'time' => $fileDate->format('H : i'),
                'by' => 'Admin',
            ];
        }

        // 3. Check-in date (tenant started living)
        if ($contract->check_in_date) {
            $checkIn = \Carbon\Carbon::parse($contract->check_in_date);
            $histories[] = [
                'type' => 'checkin',
                'date' => $checkIn->format('j') . ' ' . thaiMonth($checkIn->format('m')) . ' ' . ($checkIn->format('Y')),
                'time' => $checkIn->format('H : i'),
                'by' => $contract->tenant_name,
            ];
        }

        // 4. Start date (contract effective)
        if ($contract->start_date) {
            $startDate = \Carbon\Carbon::parse($contract->start_date);
            $histories[] = [
                'type' => 'signed',
                'date' => $startDate->format('j') . ' ' . thaiMonth($startDate->format('m')) . ' ' . ($startDate->format('Y')),
                'time' => $startDate->format('H : i'),
                'by' => $contract->tenant_name,
            ];
        }

        // Sort by date descending (newest first)
        usort($histories, function($a, $b) {
            return strcmp($b['date'] . $b['time'], $a['date'] . $a['time']);
        });

        return view('tenant.contract-history', compact('contract', 'setting', 'histories'));
    }

    /**
     * Move-out request page
     */
    public function moveout()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $setting = Setting::getInstance();

        // Check if tenant has an active moveout request
        $hasMoveoutRequest = MoveoutRequest::where('contract_id', $contract->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        return view('tenant.moveout', compact('contract', 'setting', 'hasMoveoutRequest'));
    }

    /**
     * Store move-out request
     */
    public function storeMoveout(Request $request)
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $validated = $request->validate([
            'notify_date' => 'required|date',
            'moveout_date' => 'required|date|after_or_equal:notify_date',
        ], [
            'notify_date.required' => 'กรุณาระบุวันที่แจ้งย้ายออก',
            'moveout_date.required' => 'กรุณาระบุวันที่ย้ายออกจริง',
            'moveout_date.after_or_equal' => 'วันที่ย้ายออกจริงต้องเป็นวันเดียวกันหรือหลังวันที่แจ้ง',
        ]);

        // Check for existing pending/approved request
        $existing = MoveoutRequest::where('contract_id', $contract->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->route('tenant.moveout.status')
                ->with('info', 'คุณมีรายการแจ้งย้ายออกอยู่แล้ว');
        }

        // Create moveout request
        MoveoutRequest::create([
            'contract_id' => $contract->id,
            'room_id' => $contract->room_id,
            'notify_date' => $validated['notify_date'],
            'moveout_date' => $validated['moveout_date'],
            'status' => 'pending',
        ]);

        return redirect()->route('tenant.moveout.status')->with('success', 'แจ้งย้ายออกเรียบร้อยแล้ว ทางหอพักจะติดต่อกลับเพื่อยืนยัน');
    }

    /**
     * Moveout status tracking page
     */
    public function moveoutStatus()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $setting = Setting::getInstance();

        // Get all moveout requests for this contract (newest first)
        $moveoutRequests = MoveoutRequest::where('contract_id', $contract->id)
            ->latest()
            ->get();

        return view('tenant.moveout-status', compact('contract', 'setting', 'moveoutRequests'));
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

    /**
     * Read a notification and redirect to its link
     */
    public function readNotification($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        if ($notification->link && str_starts_with($notification->link, '/')) {
            return redirect($notification->link);
        }

        return redirect()->route('tenant.dashboard');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back();
    }

    /**
     * Repair history page
     */
    public function repairIndex()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $setting = Setting::getInstance();
        $repairs = Repair::where('room_id', $contract->room_id)
            ->latest()
            ->get();

        return view('tenant.repairs', compact('contract', 'setting', 'repairs'));
    }

    /**
     * Show repair request form
     */
    public function repairCreate()
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $setting = Setting::getInstance();

        return view('tenant.repair-create', compact('contract', 'setting'));
    }

    /**
     * Store repair request
     */
    public function repairStore(Request $request)
    {
        $contract = $this->getTenantContract();

        if (!$contract) {
            return view('tenant.no-contract');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'title.required' => 'กรุณาระบุปัญหาที่แจ้งซ่อม',
            'category.required' => 'กรุณาเลือกหมวดปัญหา',
            'description.required' => 'กรุณากรอกรายละเอียด',
            'image.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'image.max' => 'ไฟล์ต้องมีขนาดไม่เกิน 5MB',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('repairs', 'public');
        }

        Repair::create([
            'room_id' => $contract->room_id,
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'status' => 'pending',
        ]);

        return redirect()->route('tenant.repairs')->with('success', 'ส่งรายการแจ้งซ่อมเรียบร้อยแล้ว');
    }
}
