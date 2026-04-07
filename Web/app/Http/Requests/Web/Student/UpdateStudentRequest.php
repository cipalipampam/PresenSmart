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
            'student_number' => ['nullable', Rule::unique('students', 'student_number')->ignore($studentId)],
            'national_student_number' => ['nullable', Rule::unique('students', 'national_student_number')->ignore($studentId)],
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
