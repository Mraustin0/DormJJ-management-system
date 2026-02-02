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
    Schema::create('bills', function (Blueprint $table) {
        $table->char('BillNo', 10)->primary();
        $table->date('BillDate');
        
        // ค่าใช้จ่ายต่างๆ
        $table->decimal('damage_fee', 8, 2)->default(0);
        $table->decimal('overdue_fee', 8, 2)->default(0);
        $table->decimal('water_price', 8, 2);
        $table->decimal('electricity_price', 8, 2);
        $table->decimal('total_price', 10, 2);
        
        $table->string('status', 20)->default('ค้างชำระ');
        $table->string('slip_image_path', 200)->nullable();
        
        // FKs
        $table->char('roomID', 3);
        $table->char('tenantID', 10);
        
        $table->foreign('roomID')->references('roomID')->on('rooms');
        $table->foreign('tenantID')->references('tenantID')->on('tenants');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
