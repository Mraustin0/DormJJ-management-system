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
            'email' => 'nullable|email|max:100',
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

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tenant_name', 'like', "%{$search}%")
                  ->orWhere('nid', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('room', function($r) use ($search) {
                      $r->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        $customers = $query->orderBy('room_id', 'asc')
                           ->paginate(10)
                           ->appends($request->all());

        return view('rooms.customers', compact('customers'));
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

        $contract->update([
            'end_date' => $request->move_out_date,
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
