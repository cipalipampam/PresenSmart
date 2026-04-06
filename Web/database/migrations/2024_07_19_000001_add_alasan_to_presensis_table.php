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
            // Ubah lat dan long menjadi nullable
            $table->decimal('lat', 10, 7)->nullable()->change();
            $table->decimal('long', 10, 7)->nullable()->change();
            
            // Tambah kolom alasan
            $table->text('alasan')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Kembalikan lat dan long ke semula
            $table->decimal('lat', 10, 7)->change();
            $table->decimal('long', 10, 7)->change();
            
            // Hapus kolom alasan
            $table->dropColumn('alasan');
        });
    }
}; 