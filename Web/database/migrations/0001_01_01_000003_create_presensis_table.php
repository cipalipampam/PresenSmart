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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->datetime('waktu');
            $table->decimal('lat', 10, 7)->nullable(); // Buat nullable
            $table->decimal('long', 10, 7)->nullable(); // Buat nullable
            $table->string('status'); // hadir, izin, sakit
            $table->text('alasan')->nullable(); // Tambah kolom alasan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
