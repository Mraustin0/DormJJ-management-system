<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 50)->unique(); // เลขที่ใบเสร็จ
            $table->date('receipt_date'); // วันที่ออกใบเสร็จ
            $table->decimal('amount_paid', 10, 2); // ยอดที่ชำระ
            $table->string('payment_method', 50)->nullable(); // วิธีชำระ (เงินสด, โอน)
            $table->string('payment_slip')->nullable(); // ไฟล์สลิปโอนเงิน
            $table->text('notes')->nullable(); // หมายเหตุ

            $table->unsignedBigInteger('bill_id');
            $table->foreign('bill_id')
                  ->references('id')
                  ->on('bills')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('received_by')->nullable(); // ผู้รับเงิน
            $table->foreign('received_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
