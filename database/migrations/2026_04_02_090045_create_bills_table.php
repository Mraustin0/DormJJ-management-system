<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->date('bill_date');
            $table->string('billing_month', 7)->nullable();
            $table->decimal('total_price', 10, 2);
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->integer('water_units')->default(0);
            $table->decimal('water_amount', 10, 2)->default(0);
            $table->integer('electric_units')->default(0);
            $table->decimal('electric_amount', 10, 2)->default(0);
            $table->decimal('room_rate', 10, 2)->default(0);
            $table->decimal('other_fees', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('payment_slip')->nullable();

            $table->unsignedBigInteger('room_id');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            $table->unique(['room_id', 'billing_month'], 'bills_room_month_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
