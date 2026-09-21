<?php

namespace App\Http\Requests\Api\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendance_date' => ['nullable', 'date_format:Y-m-d'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'integer', 'distinct', 'exists:students,id'],
            'attendances.*.status' => ['required', 'in:present,late,sick,permission,absent'],
            'attendances.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'attendances.required' => 'Daftar presensi siswa wajib diisi.',
            'attendances.min' => 'Minimal satu siswa harus diabsen.',
            'attendances.*.student_id.distinct' => 'Siswa tidak boleh dicatat lebih dari satu kali.',
            'attendances.*.status.in' => 'Status presensi siswa tidak valid.',
        ];
    }
}
