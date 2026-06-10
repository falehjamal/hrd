<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use Illuminate\Support\Collection;

class OrganizationStructureService
{
    public function getStats(): array
    {
        return [
            'total_employees' => Employee::query()->active()->count(),
            'total_departments' => OrganizationalUnit::query()
                ->active()
                ->where('code', '!=', 'ROOT')
                ->count(),
        ];
    }

    public function getCompanyRoot(): ?OrganizationalUnit
    {
        return OrganizationalUnit::query()
            ->active()
            ->where('code', 'ROOT')
            ->with($this->unitRelations())
            ->first()
            ?? OrganizationalUnit::query()
                ->active()
                ->whereNull('parent_id')
                ->with($this->unitRelations())
                ->orderBy('sort_order')
                ->orderBy('name')
                ->first();
    }

    /**
     * @return Collection<int, OrganizationalUnit>
     */
    public function buildUnitTree(): Collection
    {
        return OrganizationalUnit::query()
            ->active()
            ->whereNull('parent_id')
            ->with($this->unitRelations())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function unitRelations(): array
    {
        return [
            'employees' => fn ($q) => $q->active()->with('position')->orderBy('name'),
            'children' => fn ($q) => $q->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->with($this->unitRelations()),
        ];
    }

    /**
     * @return Collection<int, Employee>
     */
    public function buildReportingTree(): Collection
    {
        return Employee::query()
            ->active()
            ->whereNull('manager_id')
            ->with($this->reportingRelations())
            ->orderBy('name')
            ->get();
    }

    private function reportingRelations(): array
    {
        return [
            'position',
            'organizationalUnit',
            'subordinates' => fn ($q) => $q->active()
                ->with($this->reportingRelations())
                ->orderBy('name'),
        ];
    }
}
