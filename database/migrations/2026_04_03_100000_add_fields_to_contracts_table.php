<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Only add if columns don't exist
            if (!Schema::hasColumn('contracts', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('room_id');
            }
            if (!Schema::hasColumn('contracts', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('email');
            }
            if (!Schema::hasColumn('contracts', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('contracts', 'contract_duration')) {
                $table->integer('contract_duration')->default(12)->after('emergency_contact_phone');
            }
            if (!Schema::hasColumn('contracts', 'check_in_date')) {
                $table->date('check_in_date')->nullable()->after('contract_duration');
            }
            if (!Schema::hasColumn('contracts', 'contract_date')) {
                $table->date('contract_date')->nullable()->after('check_in_date');
            }
            if (!Schema::hasColumn('contracts', 'contract_file')) {
                $table->string('contract_file')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('contracts', 'idcard_file')) {
                $table->string('idcard_file')->nullable()->after('contract_file');
            }
            if (!Schema::hasColumn('contracts', 'deposit')) {
                $table->decimal('deposit', 10, 2)->nullable()->after('idcard_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $columns = ['user_id', 'emergency_contact_name', 'emergency_contact_phone',
                        'contract_duration', 'check_in_date', 'contract_date',
                        'contract_file', 'idcard_file', 'deposit'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('contracts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
