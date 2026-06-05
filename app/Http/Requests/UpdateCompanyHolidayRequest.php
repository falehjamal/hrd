<?php

namespace App\Http\Requests;

use App\Models\CompanyHoliday;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var CompanyHoliday $holiday */
        $holiday = $this->route('company_holiday');

        return [
            'date' => ['required', 'date', Rule::unique('company_holidays', 'date')->ignore($holiday->id)],
            'name' => ['required', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }
}
