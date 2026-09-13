<?php

namespace App\Http\Requests\Web\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class ResolveAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return ['action' => ['required', 'in:approve,reject']];
    }
}
