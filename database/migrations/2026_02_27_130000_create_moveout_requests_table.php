<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moveout_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('room_id');
            $table->date('notify_date'); // วันที่แจ้งย้ายออก
            $table->date('moveout_date'); // วันที่ย้ายออกจริง
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_remark')->nullable(); // หมายเหตุจากแอดมิน
            $table->decimal('deposit_return', 10, 2)->nullable(); // เงินประกันคืน
            $table->timestamp('processed_at')->nullable(); // วันที่แอดมินดำเนินการ
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moveout_requests');
    }
};
