<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'position',
        'is_teacher',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'religion',
        'address',
        'phone_number',
        'profile_picture',
    ];

    protected $casts = [
        'is_teacher' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
