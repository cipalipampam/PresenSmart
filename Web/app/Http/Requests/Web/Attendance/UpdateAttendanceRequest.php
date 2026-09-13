<?php

namespace App\Http\Requests\Web\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:present,absent,sick,permission'],
            'notes' => ['nullable', 'string', 'max:500'],
            'proof_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
