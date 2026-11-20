<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: Per Stage 01 audit rules, this migration is prepared but NOT automatically executed
     * on the live database. It requires explicit user approval before execution.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `join_requests` MODIFY COLUMN `status` ENUM('pending', 'accepted', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `join_requests` MODIFY COLUMN `status` ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending'");
        }
    }
};
