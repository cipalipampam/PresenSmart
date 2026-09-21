<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // Contoh: "X-MIPA 1", "XI-IPS 2", "XII-MIPA 1"
            $table->enum('level', ['10', '11', '12']);
            $table->string('major', 50); // MIPA, IPS, Bahasa, Umum
            $table->string('section', 10); // 1, 2, 3
            $table->string('academic_year', 20); // 2026/2027
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['level', 'major', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};

