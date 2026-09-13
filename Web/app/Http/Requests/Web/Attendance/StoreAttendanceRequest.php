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
            'status' => ['required', 'in:present,absent,sick,permission'],
            'notes' => ['nullable', 'string', 'max:500'],
            'proof_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
