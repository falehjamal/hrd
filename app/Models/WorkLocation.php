<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkLocation extends Model
{
    protected $fillable = [
        'name',
        'branch_id',
        'latitude',
        'longitude',
        'radius_meters',
        'is_active',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'radius_meters' => 'integer',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public static function clearDefaultExcept(?int $exceptId = null, ?int $branchId = null): void
    {
        static::query()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->when(
                $branchId === null,
                fn ($q) => $q->whereNull('branch_id'),
                fn ($q) => $q->where('branch_id', $branchId)
            )
            ->update(['is_default' => false]);
    }
}
