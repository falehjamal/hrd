<?php

namespace App\Models;

use App\Models\Central\TenantUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'slug',
            'app_title',
            'status',
            'suspended_at',
        ];
    }

    protected function casts(): array
    {
        return [
            'suspended_at' => 'datetime',
        ];
    }

    public function displayName(): string
    {
        return $this->app_title ?: $this->name;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function suspend(): void
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
            'suspended_at' => now(),
        ]);
    }

    public function activate(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'suspended_at' => null,
        ]);
    }

    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }
}
