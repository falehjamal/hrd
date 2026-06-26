<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RedirectsCrudModalValidation;
use Illuminate\Foundation\Http\FormRequest;

class StorePayrollPeriodRequest extends FormRequest
{
    use RedirectsCrudModalValidation;

    protected function crudModalIndexRoute(): string
    {
        return 'payroll-periods.index';
    }

    public function authorize(): bool
    {
        return $this->user()->isHrUser();
    }

    public function rules(): array
    {
        return [
            'period_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'period_year.required' => 'Tahun wajib diisi.',
            'period_month.required' => 'Bulan wajib diisi.',
        ];
    }
}
