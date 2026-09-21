<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1 = Senin, 2 = Selasa, 3 = Rabu, 4 = Kamis, 5 = Jumat, 6 = Sabtu
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 50)->nullable(); // Contoh: "Lab Komputer 1", "R.204"
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['classroom_id', 'day_of_week']);
            $table->index(['teacher_id', 'day_of_week']);
            $table->index(['day_of_week', 'start_time', 'end_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};

