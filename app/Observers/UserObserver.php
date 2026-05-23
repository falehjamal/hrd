<?php

namespace App\Observers;

use App\Models\Central\TenantUser;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        $this->syncToCentral($user);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged(['email', 'username'])) {
            $this->syncToCentral($user);
        }
    }

    public function deleted(User $user): void
    {
        if (! tenant()) {
            return;
        }

        TenantUser::query()
            ->where('tenant_id', tenant('id'))
            ->where('email', $user->getOriginal('email') ?? $user->email)
            ->delete();
    }

    protected function syncToCentral(User $user): void
    {
        if (! tenant()) {
            return;
        }

        TenantUser::query()->updateOrCreate(
            [
                'tenant_id' => tenant('id'),
                'email' => $user->email,
            ],
            [
                'username' => $user->username,
            ]
        );
    }
}
