<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RedirectsCrudModalValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeSalaryRequest extends FormRequest
{
    use RedirectsCrudModalValidation;

    protected function crudModalIndexRoute(): string
    {
        return 'employees.show';
    }

    protected function crudModalRedirectParameters(): array
    {
        return ['employee' => $this->route('employee')];
    }

    protected function crudModalSessionKey(): string
    {
        return 'open_salary_modal';
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'effective_date' => ['required', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'fixed_allowance' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }
}
