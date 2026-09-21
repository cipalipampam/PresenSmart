<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'student_id',
        'teacher_id',
        'attendance_date',
        'status',
        'notes',
        'recorded_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'recorded_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}

