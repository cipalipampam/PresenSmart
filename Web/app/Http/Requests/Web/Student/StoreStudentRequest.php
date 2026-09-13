<?php

namespace App\Http\Requests\Web\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nis' => 'nullable|unique:students,nis',
            'nisn' => 'nullable|unique:students,nisn',
            'grade' => ['required', Rule::in(config('student.grades'))],
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
