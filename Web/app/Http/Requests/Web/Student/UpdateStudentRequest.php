<?php

namespace App\Http\Requests\Web\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UpdateStudentRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        $userId = $this->route('student'); 
        $user = User::with('student')->findOrFail($userId);
        $studentId = $user->student ? $user->student->id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6',
            'nis' => ['nullable', Rule::unique('students', 'nis')->ignore($studentId)],
            'nisn' => ['nullable', Rule::unique('students', 'nisn')->ignore($studentId)],
            'grade' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048', 
        ];
    }
}
