<?php

namespace App\Http\Requests\Api\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:sick,permission',
            'notes' => 'required|string|max:500',
            'proof_image' => [
                $this->status === 'sick' ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status izin / sakit diperlukan.',
            'status.in' => 'Status harus bernilai sick (sakit) atau permission (izin).',
            'notes.required' => 'Catatan penjelasan wajib diisi.',
            'proof_image.required' => 'Surat keterangan sakit / surat dokter wajib diunggah untuk pengajuan sakit.',
            'proof_image.image' => 'File bukti/surat dokter harus berupa gambar.',
            'proof_image.mimes' => 'Format file surat harus berupa JPEG, PNG, atau JPG.',
            'proof_image.max' => 'Ukuran file surat dokter maksimal 5MB.',
        ];
    }
}
