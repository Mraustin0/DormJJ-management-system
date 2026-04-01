<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repair;
use App\Models\Notification;

class RepairController extends Controller
{
    /** รายการคำร้องแจ้งซ่อมทั้งหมด (admin) */
    public function index(Request $request)
    {
        $status = $request->input('status', '');

        $query = Repair::with('room')->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $repairs = $query->get();

        return view('rooms.repairs', compact('repairs', 'status'));
    }

    /** รายละเอียดคำร้องแจ้งซ่อม (admin) */
    public function show($id)
    {
        $repair = Repair::with('room')->findOrFail($id);
        return view('rooms.repair_detail', compact('repair'));
    }

    /** อัปเดตสถานะคำร้องแจ้งซ่อม (admin) */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:in_progress,completed,cancelled',
            'remark' => 'nullable|string|max:500',
        ]);

        $repair = Repair::with(['room', 'room.contract'])->findOrFail($id);

        // State machine: บังคับ transition ที่ถูกต้องเท่านั้น
        $allowedTransitions = [
            'pending'     => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'completed'   => [],
            'cancelled'   => [],
        ];
        if (!in_array($request->status, $allowedTransitions[$repair->status] ?? [])) {
            $statusLabel = match ($repair->status) {
                'pending'     => 'รอดำเนินการ',
                'in_progress' => 'กำลังดำเนินการ',
                'completed'   => 'เสร็จสิ้น',
                'cancelled'   => 'ยกเลิก',
                default       => $repair->status,
            };
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถเปลี่ยนสถานะได้ เนื่องจากสถานะปัจจุบันคือ "' . $statusLabel . '"',
            ], 422);
        }

        $repair->update([
            'status' => $request->status,
            'remark' => $request->remark,
        ]);

        // แจ้งเตือน tenant
        $contract = $repair->room->contract ?? null;
        if ($contract && $contract->user_id) {
            $statusLabel = match ($request->status) {
                'in_progress' => 'กำลังดำเนินการ',
                'completed'   => 'ซ่อมเสร็จสิ้น',
                'cancelled'   => 'ยกเลิก',
                default       => $request->status,
            };

            Notification::create([
                'user_id' => $contract->user_id,
                'type'    => 'repair_' . $request->status,
                'title'   => 'อัปเดตการแจ้งซ่อม',
                'message' => 'การแจ้งซ่อม "' . $repair->title . '" ห้อง '
                           . ($repair->room->room_number ?? '-')
                           . ' สถานะ: ' . $statusLabel,
                'link'    => '/tenant/repairs',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'อัปเดตสถานะเรียบร้อย']);
    }
}
