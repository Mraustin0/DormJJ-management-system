<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Rent Info
            $table->decimal('rent_per_month', 10, 2)->default(4000);
            $table->decimal('water_rate', 10, 2)->default(25);
            $table->decimal('electric_rate', 10, 2)->default(8);
            $table->decimal('late_fee_per_day', 10, 2)->default(50);
            $table->string('payment_due_day')->default('5');

            // Apartment Info
            $table->string('apartment_name')->default('เจเจพาร์ทเมนต์');
            $table->string('address')->nullable();
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Location
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
