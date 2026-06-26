<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait RedirectsCrudModalValidation
{
    abstract protected function crudModalIndexRoute(): string;

    protected function crudModalRedirectParameters(): array
    {
        return [];
    }

    protected function crudModalSessionKey(): string
    {
        return 'open_crud_modal';
    }

    protected function crudModalOpenId(): mixed
    {
        return 'create';
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()->route($this->crudModalIndexRoute(), $this->crudModalRedirectParameters())
                ->withErrors($validator)
                ->withInput()
                ->with($this->crudModalSessionKey(), $this->crudModalOpenId())
        );
    }
}
