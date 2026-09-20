<?php

namespace App\Http\Requests\Web\Attendance;

use App\Models\Attendance;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $attendance = Attendance::find($this->route('id'));
        $hasExistingProof = $attendance && !empty($attendance->proof_image);

        return [
            'recorded_at' => ['required', 'date'],
            'check_out_time' => ['nullable', 'date', 'after_or_equal:recorded_at'],
            'status' => ['required', 'in:present,absent,sick,permission'],
            'notes' => ['nullable', 'string', 'max:500'],
            'proof_image' => [
                ($this->status === 'sick' && !$hasExistingProof) ? 'required' : 'nullable',
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
            'proof_image.required' => 'Surat keterangan sakit atau surat dokter wajib diunggah jika status diubah menjadi Sakit.',
            'proof_image.file' => 'Bukti harus berupa file yang sah.',
            'proof_image.mimes' => 'Format file surat harus berupa JPG, PNG, atau PDF.',
            'proof_image.max' => 'Ukuran file surat maksimal 5MB.',
        ];
    }
}
