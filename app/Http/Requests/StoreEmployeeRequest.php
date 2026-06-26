<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RedirectsCrudModalValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreEmployeeRequest extends FormRequest
{
    use RedirectsCrudModalValidation;

    protected function crudModalIndexRoute(): string
    {
        return 'employees.index';
    }

    public function authorize(): bool
    {
        return $this->user()->isHrUser();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'send_notification' => $this->boolean('send_notification'),
            'has_hr_access' => $this->boolean('has_hr_access'),
        ]);
    }

    public function rules(): array
    {
        return [
            'employee_code' => ['required', 'string', 'max:50', 'unique:employees,employee_code'],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'national_id' => ['nullable', 'digits:16', 'unique:employees,national_id'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'join_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'send_notification' => ['nullable', 'boolean'],
            'has_hr_access' => ['nullable', 'boolean'],
        ];
    }
}
