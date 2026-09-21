<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['present', 'late', 'sick', 'permission', 'absent']);
            $table->string('notes', 255)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            // Menjamin 1 siswa tidak bisa diabsen ganda pada jadwal & tanggal yang sama
            $table->unique(['schedule_id', 'student_id', 'attendance_date'], 'uq_schedule_student_date');
            $table->index(['attendance_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_attendances');
    }
};

