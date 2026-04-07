<?php

namespace App\Services\Web;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentService
{
    public function createStudent(array $data)
    {
        $fotoPath = null;
        if (isset($data['profile_picture'])) {
            $fotoPath = $data['profile_picture']->store('pas_foto', 'public');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('siswa');

        $user->student()->create([
            'student_number' => $data['student_number'] ?? null,
            'national_student_number' => $data['national_student_number'] ?? null,
            'grade' => $data['grade'] ?? null,
            'gender' => $data['gender'] ?? null,
            'place_of_birth' => $data['place_of_birth'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'religion' => $data['religion'] ?? null,
            'address' => $data['address'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'profile_picture' => $fotoPath,
        ]);

        return $user;
    }

    public function updateStudent(User $user, array $data)
    {
        if (!empty($data['password'])) {
            $user->update(['name' => $data['name'], 'email' => $data['email'], 'password' => Hash::make($data['password'])]);
        } else {
            $user->update(['name' => $data['name'], 'email' => $data['email']]);
        }

        $currentPic = $user->student ? $user->student->profile_picture : null;
        $fotoPath = $currentPic;
        
        if (isset($data['profile_picture'])) {
            if ($currentPic) {
                Storage::disk('public')->delete($currentPic);
            }
            $fotoPath = $data['profile_picture']->store('pas_foto', 'public');
        }

        $user->student()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'student_number' => $data['student_number'] ?? null,
                'national_student_number' => $data['national_student_number'] ?? null,
                'grade' => $data['grade'] ?? null,
                'gender' => $data['gender'] ?? null,
                'place_of_birth' => $data['place_of_birth'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'religion' => $data['religion'] ?? null,
                'address' => $data['address'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'profile_picture' => $fotoPath,
            ]
        );

        return $user;
    }

    public function deleteStudent(User $user)
    {
        $currentPic = $user->student ? $user->student->profile_picture : null;
        if ($currentPic) {
            Storage::disk('public')->delete($currentPic);
        }
        $user->delete();
    }
}
