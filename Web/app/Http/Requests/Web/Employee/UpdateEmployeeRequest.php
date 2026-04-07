<?php

namespace App\Http\Requests\Web\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        $userId = $this->route('employee'); 
        $user = User::with('employee')->findOrFail($userId);
        $employeeId = $user->employee ? $user->employee->id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6',
            'role' => 'required|in:guru,staff',
            'nip' => ['nullable', Rule::unique('employees', 'nip')->ignore($employeeId)],
            'position' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048', 
        ];
    }
}
