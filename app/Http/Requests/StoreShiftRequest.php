<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RedirectsCrudModalValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreShiftRequest extends FormRequest
{
    use RedirectsCrudModalValidation;

    protected function crudModalIndexRoute(): string
    {
        return 'shifts.index';
    }

    protected function crudModalOpenId(): mixed
    {
        return 'create';
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', 'unique:shifts,code'],
            'name' => ['required', 'string', 'max:100'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'break_minutes' => ['required', 'integer', 'min:0', 'max:480'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
