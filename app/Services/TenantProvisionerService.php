<?php

namespace App\Services;

use App\Models\Central\TenantUser;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class TenantProvisionerService
{
    /**
     * @param  array{id: string, name: string, slug: string, app_title?: string|null}  $tenantData
     * @param  array{name: string, email: string, username?: string|null, password: string}  $adminData
     */
    public function create(array $tenantData, array $adminData): Tenant
    {
        $tenantId = Str::lower($tenantData['id']);

        if (Tenant::query()->where('id', $tenantId)->exists()) {
            throw new InvalidArgumentException('Tenant dengan ID tersebut sudah ada.');
        }

        // Jangan bungkus Tenant::create dalam DB::transaction — event TenantCreated
        // menjalankan CREATE DATABASE yang di MySQL meng-commit transaksi implisit.
        $tenant = null;

        try {
            $tenant = Tenant::query()->create([
                'id' => $tenantId,
                'name' => $tenantData['name'],
                'slug' => $tenantData['slug'],
                'app_title' => $tenantData['app_title'] ?? null,
                'status' => Tenant::STATUS_ACTIVE,
            ]);

            tenancy()->initialize($tenant);

            try {
                $user = User::query()->create([
                    'name' => $adminData['name'],
                    'email' => $adminData['email'],
                    'username' => $adminData['username'] ?? null,
                    'password' => Hash::make($adminData['password']),
                    'email_verified_at' => now(),
                ]);

                TenantUser::query()->updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'email' => $user->email,
                    ],
                    [
                        'username' => $user->username,
                    ]
                );
            } finally {
                tenancy()->end();
            }

            return $tenant->fresh();
        } catch (Throwable $e) {
            if ($tenant !== null) {
                $tenant->delete();
            }

            throw $e;
        }
    }
}
