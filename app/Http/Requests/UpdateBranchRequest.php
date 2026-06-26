<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RedirectsCrudModalValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBranchRequest extends FormRequest
{
    use RedirectsCrudModalValidation;

    protected function crudModalIndexRoute(): string
    {
        return 'branches.index';
    }

    protected function crudModalOpenId(): mixed
    {
        return $this->route('branch')?->getKey();
    }

    public function authorize(): bool
    {
        return $this->user()->isHrUser();
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('branches', 'code')->ignore($this->route('branch'))],
            'name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
            'is_head_office' => ['boolean'],
        ];
    }
}
