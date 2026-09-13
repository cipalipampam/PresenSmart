<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->date('attendance_date')->nullable()->after('recorded_at');
        });

        DB::table('attendances')->update([
            'attendance_date' => DB::raw('DATE(recorded_at)'),
        ]);

        Schema::table('attendances', function (Blueprint $table) {
            $table->unique(['user_id', 'attendance_date'], 'attendances_user_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendances_user_date_unique');
            $table->dropColumn('attendance_date');
        });
    }
};
