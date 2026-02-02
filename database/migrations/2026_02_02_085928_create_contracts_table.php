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
    Schema::create('contracts', function (Blueprint $table) {
        $table->char('contractID', 18)->primary();
        $table->date('contract_date');
        $table->date('start_date');
        $table->date('end_date')->nullable(); // เผื่อสัญญาปลายเปิด
        $table->string('contract_file_path', 200)->nullable();
        
        // Foreign Keys
        $table->char('roomID', 3);
        $table->char('tenantID', 10);
        
        // เชื่อมความสัมพันธ์
        $table->foreign('roomID')->references('roomID')->on('rooms')->onDelete('cascade');
        $table->foreign('tenantID')->references('tenantID')->on('tenants')->onDelete('cascade');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
