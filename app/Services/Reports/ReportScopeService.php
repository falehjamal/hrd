<?php

namespace App\Services\Reports;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ReportScopeService
{
    /**
     * @return array<int>|null null = semua karyawan aktif (HR)
     */
    public function scopedEmployeeIds(User $user): ?array
    {
        if ($user->isHrUser()) {
            return null;
        }

        $employee = $user->employee;

        if (! $employee || $employee->status !== 'active') {
            return [];
        }

        return $this->descendantEmployeeIds($employee);
    }

    public function scopedEmployeeCount(User $user): int
    {
        $ids = $this->scopedEmployeeIds($user);

        if ($ids === null) {
            return Employee::query()->active()->count();
        }

        return count($ids);
    }

    public function scopeLabel(User $user): string
    {
        if ($user->isHrUser()) {
            return 'Semua karyawan';
        }

        $count = $this->scopedEmployeeCount($user);

        return "Tim Anda ({$count} orang)";
    }

    public function applyEmployeeScope(Builder $query, User $user, string $column = 'employee_id'): void
    {
        $ids = $this->scopedEmployeeIds($user);

        if ($ids === null) {
            return;
        }

        if ($ids === []) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->whereIn($column, $ids);
    }

    public function applyEmployeeRelationScope(Builder $query, User $user, string $relation = 'employee'): void
    {
        $ids = $this->scopedEmployeeIds($user);

        if ($ids === null) {
            return;
        }

        if ($ids === []) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->whereHas($relation, fn (Builder $q) => $q->whereIn('id', $ids));
    }

    /**
     * @return array<int>
     */
    protected function descendantEmployeeIds(Employee $manager): array
    {
        $ids = [];
        $queue = $manager->subordinates()->active()->pluck('id')->all();

        while ($queue !== []) {
            $id = array_shift($queue);
            $ids[] = $id;
            $childIds = Employee::query()
                ->active()
                ->where('manager_id', $id)
                ->pluck('id')
                ->all();
            $queue = array_merge($queue, $childIds);
        }

        return array_values(array_unique($ids));
    }
}
