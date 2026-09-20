<?php

namespace App\Http\Requests\Api\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notes' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            'notes.max' => 'Catatan kepulangan maksimal 255 karakter.',
            'latitude.between' => 'Format koordinat latitude tidak valid.',
            'longitude.between' => 'Format koordinat longitude tidak valid.',
        ];
    }
}

