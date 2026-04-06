<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    // Nama tabel (opsional, jika nama tabel tidak sesuai konvensi Laravel)
    protected $table = 'presensis';

    // Kolom yang dapat diisi
    protected $fillable = [
        'user_id',
        'waktu',
        'lat',
        'long',
        'status',
        'keterangan',  // Gunakan keterangan sebagai alasan
        'bukti_foto',  // Bukti foto saja
    ];

    // Casting tipe data
    protected $casts = [
        'waktu' => 'datetime',
        'lat' => 'float',
        'long' => 'float',
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk URL bukti
    public function getBuktiUrlAttribute()
    {
        return $this->bukti_foto 
            ? url('storage/' . $this->bukti_foto) 
            : null;
    }
}
