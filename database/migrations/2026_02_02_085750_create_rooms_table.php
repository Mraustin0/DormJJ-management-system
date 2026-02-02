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
    Schema::create('rooms', function (Blueprint $table) {
        // Diagram ใช้ CHAR(3) เป็น PK
        $table->char('roomID', 3)->primary(); 
        $table->string('roomNumber', 3);
        $table->decimal('month_rate', 8, 2); // NUMBER(4,2) 
        $table->string('status', 30)->default('ว่าง');
        $table->timestamps(); // สร้าง created_at, updated_at ให้เอง
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
