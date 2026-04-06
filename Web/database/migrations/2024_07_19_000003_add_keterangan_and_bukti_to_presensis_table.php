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
        Schema::table('presensis', function (Blueprint $table) {
            // Tambah kolom keterangan
            $table->text('keterangan')->nullable()->after('alasan');
            
            // Tambah kolom bukti foto
            $table->string('bukti_foto')->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Hapus kolom keterangan
            $table->dropColumn('keterangan');
            
            // Hapus kolom bukti foto
            $table->dropColumn('bukti_foto');
        });
    }
}; 