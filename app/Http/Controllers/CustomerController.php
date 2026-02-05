<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Room;

class CustomerController extends Controller
{
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

        return response()->json(['success' => true, 'message' => 'บันทึกการย้ายออกเรียบร้อย']);
    }
}
