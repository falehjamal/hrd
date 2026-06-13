<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('leave_types', 'code')->ignore($this->route('leave_type'))],
            'name' => ['required', 'string', 'max:100'],
            'default_quota_days' => ['required', 'integer', 'min:0', 'max:365'],
            'is_paid' => ['boolean'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
