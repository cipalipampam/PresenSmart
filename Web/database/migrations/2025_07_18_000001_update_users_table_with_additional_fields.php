<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableWithAdditionalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan NIS (Nomor Induk Siswa)
            $table->integer('nis')->nullable()->unique();

            // Jenis Kelamin (Enum)
            $table->enum('jenis_kelamin', ['laki_laki', 'perempuan'])->nullable();

            // Tempat Lahir
            $table->string('tempat_lahir')->nullable();

            // Tanggal Lahir
            $table->date('tanggal_lahir')->nullable();

            // Agama (Enum)
            $table->enum('agama', [
                'islam', 
                'kristen', 
                'katholik', 
                'hindu', 
                'buddha', 
                'konghucu'
            ])->nullable();

            // Alamat Lengkap
            $table->text('alamat')->nullable();

            // Nomor Telepon
            $table->bigInteger('no_telp')->nullable();

            // Pas Foto (Path atau nama file)
            $table->string('pas_foto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            $table->dropColumn([
                'nis', 
                'jenis_kelamin', 
                'tempat_lahir', 
                'tanggal_lahir', 
                'agama', 
                'alamat', 
                'no_telp', 
                'pas_foto'
            ]);
        });
    }
} 