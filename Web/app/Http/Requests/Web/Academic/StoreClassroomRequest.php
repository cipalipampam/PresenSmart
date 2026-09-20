<?php

namespace App\Http\Requests\Web\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'level' => ['required', 'in:10,11,12'],
            'major' => ['required', 'string', 'max:50'],
            'section' => ['required', 'string', 'max:10'],
            'academic_year' => ['required', 'string', 'max:20'],
            'homeroom_teacher_id' => ['nullable', 'exists:users,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama rombel/kelas wajib diisi.',
            'level.required' => 'Tingkat kelas wajib dipilih.',
            'major.required' => 'Jurusan wajib diisi.',
            'section.required' => 'Nomor rombel wajib diisi.',
            'academic_year.required' => 'Tahun ajaran wajib diisi.',
            'homeroom_teacher_id.exists' => 'Wali kelas yang dipilih tidak valid.',
        ];
    }
}

