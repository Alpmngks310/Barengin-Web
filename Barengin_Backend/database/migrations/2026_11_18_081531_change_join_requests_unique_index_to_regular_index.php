<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('join_requests', function (Blueprint $table) {
            $table->dropUnique('uq_join_requests_activity_user');

            $table->index(
                ['activity_id', 'user_id'],
                'idx_join_requests_activity_user'
            );
        });
    }

    public function down(): void
    {
        Schema::table('join_requests', function (Blueprint $table) {
            $table->dropIndex('idx_join_requests_activity_user');

            $table->unique(
                ['activity_id', 'user_id'],
                'uq_join_requests_activity_user'
            );
        });
    }
};
