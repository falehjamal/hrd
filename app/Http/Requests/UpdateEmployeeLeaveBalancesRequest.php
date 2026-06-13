<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeLeaveBalancesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'balances' => ['required', 'array', 'min:1'],
            'balances.*.leave_type_id' => ['required', 'exists:leave_types,id'],
            'balances.*.quota_days' => ['required', 'integer', 'min:0', 'max:365'],
        ];
    }
}
