<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UpdateEmployeeRequest extends FormRequest
{
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
        $employee = $this->route('employee');

        return [
            'employee_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_code')->ignore($employee),
            ],
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($employee->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'join_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'username' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($employee->user_id),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'send_notification' => ['nullable', 'boolean'],
            'has_hr_access' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $managerId = $this->input('manager_id');
            $employee = $this->route('employee');

            if (! $managerId || ! $employee instanceof Employee) {
                return;
            }

            if ((int) $managerId === $employee->id) {
                $validator->errors()->add('manager_id', 'Atasan tidak boleh diri sendiri.');

                return;
            }

            if ($this->isSubordinate((int) $managerId, $employee->id)) {
                $validator->errors()->add('manager_id', 'Atasan tidak boleh bawahan dari karyawan ini.');
            }
        });
    }

    private function isSubordinate(int $candidateManagerId, int $employeeId): bool
    {
        $current = Employee::query()->find($candidateManagerId);

        while ($current && $current->manager_id) {
            if ($current->manager_id === $employeeId) {
                return true;
            }

            $current = $current->manager;
        }

        return false;
    }
}
