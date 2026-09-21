<?php

namespace App\Http\Requests\Api\Academic;

use Illuminate\Foundation\Http\FormRequest;

class ClassAttendanceIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendance_date' => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
