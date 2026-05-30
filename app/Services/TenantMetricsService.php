<?php

namespace App\Services;

use App\Models\Central\TenantUser;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TenantMetricsService
{
    public function forTenant(Tenant $tenant): array
    {
        $database = $this->databaseName($tenant);
        $sizeMb = $this->databaseSizeMb($database);

        $usersCount = 0;
        $lastLoginAt = TenantUser::query()
            ->where('tenant_id', $tenant->id)
            ->max('last_login_at');

        if ($this->databaseExists($database)) {
            tenancy()->initialize($tenant);

            try {
                $usersCount = User::query()->count();
            } finally {
                tenancy()->end();
            }
        }

        return [
            'database' => $database,
            'database_exists' => $this->databaseExists($database),
            'size_mb' => $sizeMb,
            'users_count' => $usersCount,
            'tenant_users_count' => TenantUser::query()->where('tenant_id', $tenant->id)->count(),
            'last_login_at' => $lastLoginAt,
        ];
    }

    public function databaseName(Tenant $tenant): string
    {
        return config('tenancy.database.prefix').$tenant->id;
    }

    public function databaseSizeMb(string $database): ?float
    {
        if (! $this->databaseExists($database)) {
            return null;
        }

        $result = DB::connection('central')->selectOne(
            'SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
             FROM information_schema.tables
             WHERE table_schema = ?',
            [$database]
        );

        return $result?->size_mb !== null ? (float) $result->size_mb : null;
    }

    public function databaseExists(string $database): bool
    {
        $result = DB::connection('central')->selectOne(
            'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?',
            [$database]
        );

        return $result !== null;
    }
}
