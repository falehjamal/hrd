<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant;

class TenantUser extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'tenant_id',
        'email',
        'username',
    ];

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
