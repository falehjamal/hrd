<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RedirectsCrudModalValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    use RedirectsCrudModalValidation;

    protected function crudModalIndexRoute(): string
    {
        return 'leave-requests.index';
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];

        if ($this->user()->isHrUser()) {
            $rules['employee_id'] = ['required', 'exists:employees,id'];
        }

        return $rules;
    }
}
