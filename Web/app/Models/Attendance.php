<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'recorded_at', 
        'latitude', 
        'longitude', 
        'status', 
        'notes', 
        'proof_image',
        'is_approved',
        'is_late',
        'check_out_time'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'check_out_time' => 'datetime',
        'is_approved' => 'boolean',
        'is_late' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
