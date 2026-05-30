<?php

namespace App\Models\Central;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantUser extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'tenant_id',
        'email',
        'username',
        'last_login_at',
    ];

    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function findByLogin(string $login): ?self
    {
        return static::query()
            ->where('email', $login)
            ->orWhere('username', $login)
            ->first();
    }
}
