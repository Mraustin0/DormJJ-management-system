<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make email nullable while keeping the existing unique index
        DB::statement('ALTER TABLE users MODIFY COLUMN email VARCHAR(100) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users MODIFY COLUMN email VARCHAR(100) NOT NULL');
    }
};
