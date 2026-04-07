<?php

namespace App\Http\Requests\Api\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'Koordinat lokasi diperlukan.',
            'longitude.required' => 'Koordinat lokasi diperlukan.',
            'proof_image.image' => 'File bukti harus berupa gambar.',
            'proof_image.max' => 'Ukuran file gambar maksimal 5MB.',
        ];
    }
}
