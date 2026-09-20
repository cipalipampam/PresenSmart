<?php

namespace App\Http\Requests\Web\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'recorded_at' => ['required', 'date'],
            'check_out_time' => ['nullable', 'date', 'after_or_equal:recorded_at'],
            'status' => ['required', 'in:present,absent,sick,permission'],
            'notes' => ['nullable', 'string', 'max:500'],
            'proof_image' => [
                $this->status === 'sick' ? 'required' : 'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
            'scope' => ['nullable', 'in:siswa,employee'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof_image.required' => 'Surat keterangan sakit atau surat dokter wajib diunggah untuk status Sakit.',
            'proof_image.file' => 'Bukti harus berupa file dokumen atau gambar yang sah.',
            'proof_image.mimes' => 'Format file surat harus berupa JPG, PNG, atau PDF.',
            'proof_image.max' => 'Ukuran file surat maksimal 5MB.',
        ];
    }
}
