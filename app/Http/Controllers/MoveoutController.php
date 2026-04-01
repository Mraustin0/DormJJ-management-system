<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MoveoutRequest;
use App\Models\Notification;

class MoveoutController extends Controller
{
    /** รายการคำร้องย้ายออกทั้งหมด */
    public function index(Request $request)
    {
        $status = $request->input('status', '');

        $query = MoveoutRequest::with(['contract', 'room'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->get();

        return view('rooms.moveout_requests', compact('requests', 'status'));
    }

    /** รายละเอียดคำร้องย้ายออก */
    public function show($id)
    {
        $moveout = MoveoutRequest::with(['contract', 'room'])->findOrFail($id);
        return view('rooms.moveout_detail', compact('moveout'));
    }

    /** อนุมัติคำร้องย้ายออก */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'outstanding_debt' => 'nullable|numeric|min:0',
            'fine_amount'      => 'nullable|numeric|min:0',
            'return_date'      => 'required|date',
            'admin_remark'     => 'nullable|string|max:500',
        ]);

        $moveout = MoveoutRequest::with(['contract', 'room'])->findOrFail($id);

        $deposit          = (float)($moveout->contract->deposit ?? 0);
        $debt             = (float)($request->outstanding_debt ?? 0);
        $fine             = (float)($request->fine_amount ?? 0);
        $totalDeductions  = $debt + $fine;
        $depositReturn    = max(0, $deposit - $totalDeductions);
        $remainingDebt    = max(0, $totalDeductions - $deposit);

        // ถ้ายอดค้างเกินมัดจำ ให้บันทึกยอดค้างใน remark
        $remark = $request->admin_remark ?? '';
        if ($remainingDebt > 0) {
            $note = 'ยอดค้างชำระหลังหักมัดจำ: ' . number_format($remainingDebt, 2) . ' บาท';
            $remark = $remark ? $remark . ' | ' . $note : $note;
        }

        // อัปเดตสถานะคำร้อง
        $moveout->update([
            'status'         => 'approved',
            'deposit_return' => $depositReturn,
            'admin_remark'   => $remark ?: null,
            'processed_at'   => now(),
        ]);

        // อัปเดตวันสิ้นสุดสัญญา + ปิดสัญญา
        $contract = $moveout->contract;
        $contract->update([
            'end_date' => $moveout->moveout_date,
            'status'   => 'expired',             // ← แก้: ต้องปิดสัญญาด้วย
        ]);

        // เปลี่ยนสถานะห้องเป็นว่าง
        $moveout->room->update([
            'status'         => 'ว่าง',
            'payment_status' => null,
        ]);

        // แจ้งเตือน tenant
        if ($contract->user_id) {
            Notification::create([
                'user_id' => $contract->user_id,
                'type'    => 'moveout_approved',
                'title'   => 'การย้ายออกได้รับการอนุมัติ',
                'message' => 'คำร้องย้ายออกห้อง ' . ($moveout->room->room_number ?? '-')
                           . ' ได้รับการอนุมัติแล้ว วันที่ย้ายออก: '
                           . $moveout->moveout_date->format('d/m/Y'),
                'link'    => '/tenant/moveout/status',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'อนุมัติการย้ายออกเรียบร้อย']);
    }

    /** ปฏิเสธคำร้องย้ายออก */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_remark' => 'required|string|max:500',
        ]);

        $moveout = MoveoutRequest::with(['contract', 'room'])->findOrFail($id);

        $moveout->update([
            'status'       => 'rejected',
            'admin_remark' => $request->admin_remark,
            'processed_at' => now(),
        ]);

        // แจ้งเตือน tenant
        $contract = $moveout->contract;
        if ($contract && $contract->user_id) {
            Notification::create([
                'user_id' => $contract->user_id,
                'type'    => 'moveout_rejected',
                'title'   => 'คำร้องย้ายออกถูกปฏิเสธ',
                'message' => 'คำร้องย้ายออกห้อง ' . ($moveout->room->room_number ?? '-')
                           . ' ถูกปฏิเสธ เหตุผล: ' . $request->admin_remark,
                'link'    => '/tenant/moveout/status',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'ปฏิเสธคำร้องเรียบร้อย']);
    }
}
