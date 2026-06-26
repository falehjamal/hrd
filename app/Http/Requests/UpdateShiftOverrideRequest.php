<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RedirectsCrudModalValidation;
use App\Models\EmployeeShiftOverride;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class UpdateShiftOverrideRequest extends FormRequest
{
    use RedirectsCrudModalValidation;

    protected function crudModalIndexRoute(): string
    {
        return 'shift-overrides.index';
    }

    protected function crudModalOpenId(): mixed
    {
        return $this->route('shift_override')?->getKey();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var EmployeeShiftOverride $override */
        $override = $this->route('shift_override');

        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => [
                'required',
                'date',
                Rule::unique('employee_shift_overrides', 'date')
                    ->where('employee_id', $this->input('employee_id'))
                    ->ignore($override->id),
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
