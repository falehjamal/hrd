<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeWeeklyShiftRequest extends FormRequest
{
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
