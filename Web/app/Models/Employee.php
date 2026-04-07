<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nip', 'position', 'gender', 'place_of_birth', 
        'date_of_birth', 'religion', 'address', 'phone_number', 'profile_picture'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
