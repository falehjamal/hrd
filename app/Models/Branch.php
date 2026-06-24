<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'phone',
        'is_active',
        'is_head_office',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_head_office' => 'boolean',
        ];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function workLocations(): HasMany
    {
        return $this->hasMany(WorkLocation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function clearHeadOfficeExcept(?int $exceptId = null): void
    {
        static::query()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->update(['is_head_office' => false]);
    }
}
