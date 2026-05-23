<?php

namespace App\Auth;

use App\Models\Tenant;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class TenantAwareUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier, $remember = false): ?Authenticatable
    {
        if (! $this->ensureTenancyInitialized()) {
            return null;
        }

        return parent::retrieveById($identifier, $remember);
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        if (! $this->ensureTenancyInitialized()) {
            return null;
        }

        return parent::retrieveByToken($identifier, $token);
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (tenancy()->initialized || $this->ensureTenancyInitialized()) {
            return parent::retrieveByCredentials($credentials);
        }

        return null;
    }

    protected function ensureTenancyInitialized(): bool
    {
        if (tenancy()->initialized) {
            return true;
        }

        $tenantId = session('tenant_id');

        if (! $tenantId) {
            return false;
        }

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            return false;
        }

        tenancy()->initialize($tenant);

        return true;
    }
}
