<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! $this->user()->employee;
    }

    public function rules(): array
    {
        return [
            'mail_enabled' => ['nullable', 'boolean'],
            'mail_host' => ['nullable', 'string', 'max:150'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_encryption' => ['nullable', Rule::in(['tls', 'ssl', ''])],
            'mail_username' => ['nullable', 'email', 'max:150'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['nullable', 'email', 'max:150'],
            'mail_from_name' => ['nullable', 'string', 'max:150'],
            'wa_enabled' => ['nullable', 'boolean'],
            'wa_provider' => ['nullable', 'string', 'max:50'],
            'wa_base_url' => ['nullable', 'string', 'max:255'],
            'wa_token' => ['nullable', 'string', 'max:255'],
            'wa_sender' => ['nullable', 'string', 'max:50'],
        ];
    }
}
