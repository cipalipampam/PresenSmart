<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['user_id', 'recorded_at'], 'attendances_user_recorded_at_index');
            $table->index(['recorded_at', 'status'], 'attendances_recorded_at_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_user_recorded_at_index');
            $table->dropIndex('attendances_recorded_at_status_index');
        });
    }
};
