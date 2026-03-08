<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Notification;
use App\Models\Room;
use App\Models\User;
use App\Models\MeterReading;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function create()
    {
        $vacantRooms = Room::where('status', 'ว่าง')->orderBy('room_number', 'asc')->get();
        return view('rooms.tenants_create', compact('vacantRooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100|unique:users,email',
            'nid' => 'required|string|max:20',
            'username' => 'nullable|string|max:255|unique:users,username|required_with:password',
            'password' => 'nullable|string|min:6|required_with:username',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'room_id' => 'required|exists:rooms,id',
            'contract_duration' => 'required|in:6,12',
            'check_in_date' => 'required|date',
            'contract_date' => 'required|date',
            'tenant_status' => 'required|in:active,reserved',
            'initial_electric_meter' => 'nullable|numeric|min:0',
            'initial_water_meter' => 'nullable|numeric|min:0',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'idcard_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();
        try {
            // Create user login account if both username and password are provided
            $userId = null;
            $hasLoginAccount = false;
            $email = !empty($request->email) ? $request->email : null;

            if (!empty($request->username) && !empty($request->password)) {
                $user = User::create([
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'email' => $email,
                    'tenant_role' => true,
                ]);
                $userId = $user->id;
                $hasLoginAccount = true;
            }

            // Handle file uploads
            $contractFilePath = null;
            $idcardFilePath = null;

            if ($request->hasFile('contract_file')) {
                $contractFilePath = $request->file('contract_file')->store('contracts', 'public');
            }
            if ($request->hasFile('idcard_file')) {
                $idcardFilePath = $request->file('idcard_file')->store('idcards', 'public');
            }

            // คำนวณ start_date / end_date
            $startDate = Carbon::parse($request->check_in_date);
            $endDate   = $startDate->copy()->addMonths((int) $request->contract_duration);

            // ตรวจสอบสัญญาที่ทับซ้อน
            $overlap = Contract::where('room_id', $request->room_id)
                ->whereIn('status', ['active', 'ending'])
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereNotNull('start_date')
                      ->whereNotNull('end_date')
                      ->where('start_date', '<=', $endDate)
                      ->where('end_date', '>=', $startDate);
                })->exists();

            if ($overlap) {
                DB::rollBack();
                return back()->withErrors(['check_in_date' => 'ห้องนี้มีสัญญาที่ทับซ้อนกับช่วงเวลาที่เลือกอยู่แล้ว'])->withInput();
            }

            // Create contract
            $contract = Contract::create([
                'room_id' => $request->room_id,
                'tenant_name' => $request->tenant_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'nid' => $request->nid,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'user_id' => $userId,
                'contract_duration' => $request->contract_duration,
                'check_in_date' => $request->check_in_date,
                'contract_date' => $request->contract_date,
                'start_date'    => $startDate->toDateString(),
                'end_date'      => $endDate->toDateString(),
                'status'        => 'active',
                'contract_file' => $contractFilePath,
                'idcard_file' => $idcardFilePath,
            ]);

            // Update room status
            $room = Room::find($request->room_id);
            $room->update([
                'status' => $request->tenant_status == 'active' ? 'ไม่ว่าง' : 'จอง',
            ]);

            // Always create initial meter reading for the check-in month
            $billingMonth = Carbon::parse($request->check_in_date)->format('Y-m');
            MeterReading::updateOrCreate(
                ['room_id' => $request->room_id, 'billing_month' => $billingMonth],
                [
                    'water_prev' => (int) ($request->initial_water_meter ?? 0),
                    'elec_prev'  => (int) ($request->initial_electric_meter ?? 0),
                    'water_curr' => (int) ($request->initial_water_meter ?? 0),
                    'elec_curr'  => (int) ($request->initial_electric_meter ?? 0),
                    'water_unit' => 0,
                    'elec_unit'  => 0,
                ]
            );

            // Send notification to tenant
            if ($userId) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'contract_created',
                    'title' => 'สัญญาเช่าห้อง ' . $room->room_number,
                    'message' => 'สัญญาเช่าของคุณถูกสร้างเรียบร้อยแล้ว ยินดีต้อนรับเข้าสู่หอพัก',
                    'link' => route('tenant.contract.detail'),
                ]);
            }

            DB::commit();
            $message = $hasLoginAccount
                ? 'สร้างบัญชีผู้เช่าเรียบร้อยแล้ว (มีบัญชี Login: ' . $request->username . ')'
                : 'สร้างข้อมูลผู้เช่าเรียบร้อยแล้ว (ไม่มีบัญชี Login)';
            return redirect()->route('rooms.customers')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Customer create error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง'])->withInput();
        }
    }

    public function index(Request $request)
    {
        $query = Contract::with('room');

        // ── Filter: search ────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tenant_name', 'like', "%{$search}%")
                  ->orWhere('nid',        'like', "%{$search}%")
                  ->orWhere('phone',      'like', "%{$search}%")
                  ->orWhereHas('room', function ($r) use ($search) {
                      $r->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        // ── Filter: status (พักอยู่ / ย้ายออก) ───────────────────────────────
        $statusFilter = $request->input('status', '');
        if ($statusFilter === 'active') {
            $query->whereIn('status', ['active', 'ending']);
        } elseif ($statusFilter === 'expired') {
            $query->where('status', 'expired');
        }

        $customers = $query->orderBy('room_id', 'asc')
                           ->paginate(10)
                           ->appends($request->all());

        return view('rooms.customers', compact('customers', 'statusFilter'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:contracts,id',
            'tenant_name' => 'required|string|max:255',
            'nid' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
        ]);

        $contract = Contract::find($request->id);
        if ($contract) {
            $contract->update([
                'tenant_name' => $request->tenant_name,
                'nid' => $request->nid,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function editForm($id)
    {
        $contract = Contract::with(['room', 'user'])->findOrFail($id);
        $vacantRooms = Room::where('status', 'ว่าง')->orderBy('room_number', 'asc')->get();
        return view('rooms.customer_edit', compact('contract', 'vacantRooms'));
    }

    public function updateFull(Request $request, $id)
    {
        $contract = Contract::with(['room', 'user'])->findOrFail($id);

        $request->validate([
            'tenant_name'             => 'required|string|max:255',
            'phone'                   => 'nullable|string|max:20',
            'email'                   => 'nullable|email|max:100',
            'nid'                     => 'nullable|string|max:20',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'contract_date'           => 'nullable|date',
            'password'                => 'nullable|string|min:6',
            'contract_file'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'idcard_file'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            // เข้าพักใหม่
            'new_room_id'             => 'nullable|exists:rooms,id',
            'new_check_in_date'       => 'nullable|date|required_with:new_room_id',
            'new_contract_duration'   => 'nullable|in:6,12|required_with:new_room_id',
            'new_contract_date'       => 'nullable|date',
        ]);

        // ── อัปเดตข้อมูลส่วนตัวในสัญญาเดิมเสมอ ────────────────────────────────
        $baseData = [
            'tenant_name'             => $request->tenant_name,
            'phone'                   => $request->phone,
            'email'                   => $request->email,
            'nid'                     => $request->nid,
            'emergency_contact_name'  => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'contract_date'           => $request->contract_date,
        ];
        $contract->update($baseData);

        // อัปเดตรหัสผ่านถ้ากรอกมาและมี user account
        if ($contract->user_id && !empty($request->password)) {
            $user = User::find($contract->user_id);
            if ($user) {
                $user->update(['password' => Hash::make($request->password)]);
            }
        }

        // ── สร้างสัญญาใหม่เมื่อผู้เช่าเก่ากลับมาพัก (เฉพาะ expired) ─────────────
        if ($contract->status === 'expired' && $request->filled('new_room_id')) {
            DB::beginTransaction();
            try {
                $startDate = Carbon::parse($request->new_check_in_date);
                $endDate   = $startDate->copy()->addMonths((int) $request->new_contract_duration);

                // ตรวจสอบสัญญาทับซ้อน
                $overlap = Contract::where('room_id', $request->new_room_id)
                    ->whereIn('status', ['active', 'ending'])
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $endDate)
                          ->where('end_date',   '>=', $startDate);
                    })->exists();

                if ($overlap) {
                    DB::rollBack();
                    return back()
                        ->withErrors(['new_room_id' => 'ห้องนี้มีสัญญาที่ทับซ้อนในช่วงเวลาที่เลือก'])
                        ->withInput();
                }

                // Handle file uploads
                $contractFilePath = null;
                $idcardFilePath   = null;
                if ($request->hasFile('contract_file')) {
                    $contractFilePath = $request->file('contract_file')->store('contracts', 'public');
                }
                if ($request->hasFile('idcard_file')) {
                    $idcardFilePath = $request->file('idcard_file')->store('idcards', 'public');
                }

                $newContract = Contract::create([
                    'room_id'                 => $request->new_room_id,
                    'tenant_name'             => $request->tenant_name,
                    'phone'                   => $request->phone,
                    'email'                   => $request->email,
                    'nid'                     => $request->nid,
                    'emergency_contact_name'  => $request->emergency_contact_name,
                    'emergency_contact_phone' => $request->emergency_contact_phone,
                    'user_id'                 => $contract->user_id,
                    'contract_duration'       => $request->new_contract_duration,
                    'check_in_date'           => $request->new_check_in_date,
                    'contract_date'           => $request->new_contract_date ?? $request->new_check_in_date,
                    'start_date'              => $startDate->toDateString(),
                    'end_date'                => $endDate->toDateString(),
                    'status'                  => 'active',
                    'contract_file'           => $contractFilePath,
                    'idcard_file'             => $idcardFilePath,
                ]);

                // อัปเดตสถานะห้องใหม่
                $newRoom = Room::find($request->new_room_id);
                $newRoom->update([
                    'status' => $startDate->isAfter(Carbon::today()) ? 'รอเข้าพัก' : 'ไม่ว่าง',
                ]);

                // แจ้งเตือน user ถ้ามี account
                if ($contract->user_id) {
                    Notification::create([
                        'user_id' => $contract->user_id,
                        'type'    => 'contract_created',
                        'title'   => 'สัญญาเช่าห้อง ' . $newRoom->room_number,
                        'message' => 'สัญญาเช่าใหม่ของคุณถูกสร้างเรียบร้อยแล้ว ยินดีต้อนรับกลับสู่หอพัก',
                        'link'    => route('tenant.contract.detail'),
                    ]);
                }

                DB::commit();
                return redirect()
                    ->route('customers.editForm', $newContract->id)
                    ->with('success', 'สร้างสัญญาใหม่สำหรับ "' . $request->tenant_name . '" ห้อง ' . $newRoom->room_number . ' เรียบร้อยแล้ว');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Contract renewal error: ' . $e->getMessage());
                return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง'])->withInput();
            }
        }

        // ── กรณีแก้ไขปกติ (ไม่ได้สร้างสัญญาใหม่) ─────────────────────────────
        $editData = [];
        if ($request->hasFile('contract_file')) {
            $editData['contract_file'] = $request->file('contract_file')->store('contracts', 'public');
        }
        if ($request->hasFile('idcard_file')) {
            $editData['idcard_file'] = $request->file('idcard_file')->store('idcards', 'public');
        }
        if (!empty($editData)) {
            $contract->update($editData);
        }

        return redirect()->route('rooms.customers')->with('success', 'แก้ไขข้อมูลผู้เช่า "' . $contract->tenant_name . '" เรียบร้อยแล้ว');
    }

    public function destroyContract($id)
    {
        $contract = Contract::with('room')->findOrFail($id);

        // ถ้าสัญญายังใช้งานอยู่ → คืนสถานะห้องเป็น "ว่าง"
        if ($contract->room && in_array($contract->status, ['active', 'ending'])) {
            $contract->room->update([
                'status'         => 'ว่าง',
                'payment_status' => null,
            ]);
        }

        $name = $contract->tenant_name;
        $contract->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูล "' . $name . '" เรียบร้อยแล้ว',
        ]);
    }

    public function moveOut(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'move_out_date' => 'required|date',
        ]);

        $contract = Contract::with('room')->find($request->contract_id);

        if (!$contract) {
            return response()->json(['success' => false, 'message' => 'ไม่พบสัญญา'], 404);
        }

        if ($contract->status === 'expired') {
            return response()->json(['success' => false, 'message' => 'สัญญานี้สิ้นสุดแล้ว ไม่สามารถดำเนินการได้'], 422);
        }

        $contract->update([
            'end_date' => $request->move_out_date,
            'status'   => 'expired',
        ]);

        $contract->room->update([
            'status' => 'ว่าง',
            'payment_status' => null,
        ]);

        // Send notification to tenant
        if ($contract->user_id) {
            Notification::create([
                'user_id' => $contract->user_id,
                'type' => 'moveout_processed',
                'title' => 'ดำเนินการย้ายออกเรียบร้อย',
                'message' => 'การย้ายออกห้อง ' . $contract->room->room_number . ' ได้รับการดำเนินการแล้ว',
                'link' => route('tenant.dashboard'),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'บันทึกการย้ายออกเรียบร้อย']);
    }
}
