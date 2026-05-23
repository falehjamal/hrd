<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalary extends Model
{
    protected $fillable = [
        'employee_id',
        'effective_date',
        'basic_salary',
        'fixed_allowance',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'basic_salary' => 'decimal:2',
            'fixed_allowance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected function totalSalary(): Attribute
    {
        return Attribute::get(fn () => (float) $this->basic_salary + (float) $this->fixed_allowance);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function deactivateOthersForEmployee(int $employeeId, ?int $exceptId = null): void
    {
        static::query()
            ->where('employee_id', $employeeId)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->update(['is_active' => false]);
    }
}
