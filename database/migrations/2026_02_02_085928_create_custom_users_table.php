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
    Schema::create('users', function (Blueprint $table) {
        $table->char('userID', 10)->primary();
        $table->string('username', 50);
        $table->string('email', 30)->unique();
        $table->string('password', 255); // Laravel Hash 30 
        
        // Roles (ตาม Diagram แยกคอลัมน์)
        $table->string('admin_role', 20)->nullable();
        $table->string('tenant_role', 20)->nullable();
        $table->string('staff_role', 20)->nullable();
        
        // FK
        $table->char('tenantID', 10)->nullable();
        $table->foreign('tenantID')->references('tenantID')->on('tenants')->onDelete('set null');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_users');
    }
};
