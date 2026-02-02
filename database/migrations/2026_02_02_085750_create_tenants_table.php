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
    Schema::create('tenants', function (Blueprint $table) {
        $table->char('tenantID', 10)->primary();
        $table->char('SSN', 13)->unique(); // เลขบัตรประชาชนควร Unique
        $table->string('tenantName', 30);
        $table->char('telnumber', 15);
        $table->string('email', 100)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
