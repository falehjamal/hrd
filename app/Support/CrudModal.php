<?php

namespace App\Support;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CrudModal
{
    public static function validationRedirect(Validator $validator, string $indexRoute, mixed $openId = 'create'): never
    {
        throw new HttpResponseException(
            redirect()->route($indexRoute)
                ->withErrors($validator)
                ->withInput()
                ->with('open_crud_modal', $openId ?? 'create')
        );
    }

    public static function openId(): mixed
    {
        if (session()->has('open_crud_modal')) {
            return session('open_crud_modal');
        }

        return null;
    }
}
