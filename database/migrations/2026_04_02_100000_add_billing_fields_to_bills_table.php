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
        Schema::table('bills', function (Blueprint $table) {
            $table->string('billing_month', 7)->nullable()->after('bill_date'); // Format: Y-m
            $table->decimal('total_amount', 10, 2)->nullable()->after('total_price');
            $table->integer('water_units')->default(0)->after('total_amount');
            $table->decimal('water_amount', 10, 2)->default(0)->after('water_units');
            $table->integer('electric_units')->default(0)->after('water_amount');
            $table->decimal('electric_amount', 10, 2)->default(0)->after('electric_units');
            $table->decimal('room_rate', 10, 2)->default(0)->after('electric_amount');
            $table->decimal('other_fees', 10, 2)->default(0)->after('room_rate');
            $table->date('due_date')->nullable()->after('other_fees');

            // Add unique constraint for room + month
            $table->unique(['room_id', 'billing_month'], 'bills_room_month_unique');
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropUnique('bills_room_month_unique');
            $table->dropColumn([
                'billing_month',
                'total_amount',
                'water_units',
                'water_amount',
                'electric_units',
                'electric_amount',
                'room_rate',
                'other_fees',
                'due_date',
            ]);
        });
    }
};
