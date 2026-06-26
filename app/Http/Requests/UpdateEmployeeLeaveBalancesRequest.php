<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RedirectsCrudModalValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeLeaveBalancesRequest extends FormRequest
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
        return 'open_leave_balance_modal';
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
        return [
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'balances' => ['required', 'array', 'min:1'],
            'balances.*.leave_type_id' => ['required', 'exists:leave_types,id'],
            'balances.*.quota_days' => ['required', 'integer', 'min:0', 'max:365'],
        ];
    }
}
