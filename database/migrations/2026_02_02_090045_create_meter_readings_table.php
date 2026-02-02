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
    Schema::create('meter_readings', function (Blueprint $table) {
        $table->char('meterID', 18)->primary();
        
        // เลขมิเตอร์
        $table->integer('water_start_unit');
        $table->integer('water_end_unit');
        $table->integer('electricity_start_unit');
        $table->integer('electricity_end_unit');
        
        // จำนวนหน่วยที่ใช้
        $table->integer('water_unit');
        $table->integer('electricity_unit');
        
        $table->string('status', 20);
        $table->char('billing_month', 7);
        
        // FKs
        $table->char('roomID', 3);
        $table->char('BillNo', 10); // เชื่อมไปที่ Bill
        
        $table->foreign('roomID')->references('roomID')->on('rooms');
        $table->foreign('BillNo')->references('BillNo')->on('bills')->onDelete('cascade');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
