<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RedirectsCrudModalValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeWeeklyShiftRequest extends FormRequest
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
        return 'open_weekly_shift_modal';
    }

    protected function crudModalOpenId(): mixed
    {
        return '1';
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        foreach (range(1, 7) as $day) {
            $rules["shifts.{$day}"] = ['nullable', 'exists:shifts,id'];
        }

        return $rules;
    }
}
