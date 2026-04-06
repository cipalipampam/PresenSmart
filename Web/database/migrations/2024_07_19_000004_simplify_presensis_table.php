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
            // Hapus kolom alasan
            $table->dropColumn('alasan');
            
            // Hapus kolom bukti
            $table->dropColumn('bukti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Kembalikan kolom alasan
            $table->text('alasan')->nullable()->after('status');
            
            // Kembalikan kolom bukti
            $table->string('bukti')->nullable()->after('keterangan');
        });
    }
}; 