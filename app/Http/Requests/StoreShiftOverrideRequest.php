<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class StoreShiftOverrideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => [
                'required',
                'date',
                Rule::unique('employee_shift_overrides', 'date')
                    ->where('employee_id', $this->input('employee_id')),
            ],
            'is_day_off' => ['boolean'],
            'shift_id' => [new RequiredIf(fn () => ! $this->boolean('is_day_off')), 'nullable', 'exists:shifts,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->boolean('is_day_off')) {
            $this->merge(['shift_id' => null]);
        }
    }
}
