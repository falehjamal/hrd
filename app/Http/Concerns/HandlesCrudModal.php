<?php

namespace App\Http\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

trait HandlesCrudModal
{
    abstract protected function crudModalIndexRoute(): string;

    abstract protected function crudModalResourceKey(): string;

    protected function crudModalJson(object $model): JsonResponse
    {
        return response()->json([
            $this->crudModalResourceKey() => $model,
        ]);
    }

    protected function crudModalCreateRedirect(): RedirectResponse
    {
        return redirect()->route($this->crudModalIndexRoute())
            ->with('open_crud_modal', 'create');
    }

    protected function crudModalEditRedirect(object $model): RedirectResponse
    {
        return redirect()->route($this->crudModalIndexRoute())
            ->with('open_crud_modal', $model->getKey());
    }
}
