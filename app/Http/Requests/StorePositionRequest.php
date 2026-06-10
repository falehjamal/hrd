<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isHrUser();
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', 'unique:positions,code'],
            'name' => ['required', 'string', 'max:100'],
            'level' => ['required', 'integer', 'min:1', 'max:99'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }
}
