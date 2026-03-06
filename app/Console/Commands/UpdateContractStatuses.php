<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateContractStatuses extends Command
{
    protected $signature   = 'contracts:update-status';
    protected $description = 'อัปเดต status สัญญา: active → ending (≤30 วัน) → expired + คืนสถานะห้องว่าง';

    public function handle(): int
    {
        $today      = Carbon::today();
        $soonLimit  = $today->copy()->addDays(30);

        // ── 1. expired: end_date ผ่านไปแล้ว ──────────────────────────────────
        $expiredContracts = Contract::whereIn('status', ['active', 'ending'])
            ->whereNotNull('end_date')
            ->where('end_date', '<', $today)
            ->get();

        foreach ($expiredContracts as $contract) {
            $contract->update(['status' => 'expired']);

            // คืนสถานะห้องเป็น "ว่าง" ถ้าไม่มีสัญญา active/ending อื่นอยู่
            $hasActiveContract = Contract::where('room_id', $contract->room_id)
                ->whereIn('status', ['active', 'ending'])
                ->exists();

            if (! $hasActiveContract) {
                Room::where('id', $contract->room_id)
                    ->update(['status' => 'ว่าง']);

                $this->line("  ห้อง {$contract->room->room_number}: สัญญา #{$contract->id} หมดอายุ → ห้องว่าง");
            }
        }

        $expiredCount = $expiredContracts->count();

        // ── 2. ending: end_date เหลือ ≤30 วัน (แต่ยังไม่หมด) ────────────────
        $endingCount = Contract::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '>=', $today)
            ->where('end_date', '<=', $soonLimit)
            ->update(['status' => 'ending']);

        $this->info("contracts:update-status เสร็จสิ้น");
        $this->line("  → expired: {$expiredCount} สัญญา");
        $this->line("  → ending:  {$endingCount} สัญญา");

        return Command::SUCCESS;
    }
}
