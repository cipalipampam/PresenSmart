<?php

namespace App\Http\Requests\Api\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class HistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|between:2000,2100',
        ];
    }

    public function messages(): array
    {
        return [
            'month.integer' => 'Parameter bulan harus berupa angka.',
            'month.between' => 'Bulan harus bernilai antara 1 sampai 12.',
            'year.integer' => 'Parameter tahun harus berupa angka.',
            'year.between' => 'Tahun harus bernilai antara tahun 2000 sampai 2100.',
        ];
    }
}

