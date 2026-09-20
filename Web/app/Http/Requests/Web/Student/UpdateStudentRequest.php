<?php

namespace App\Http\Requests\Web\Student;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap siswa wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar untuk pengguna lain.',
            'password.min' => 'Kata sandi baru minimal terdiri dari 6 karakter.',
            'nis.unique' => 'Nomor Induk Siswa (NIS) ini sudah terdaftar.',
            'nisn.unique' => 'NISN ini sudah terdaftar untuk siswa lain.',
            'grade.required' => 'Kelas / tingkatan wajib dipilih.',
            'profile_picture.image' => 'Foto profil harus berupa file gambar.',
            'profile_picture.max' => 'Ukuran foto profil maksimal 2MB.',
        ];
    }
}
