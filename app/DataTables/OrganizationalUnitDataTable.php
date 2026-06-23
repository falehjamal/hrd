<?php

namespace App\DataTables;

use App\Models\OrganizationalUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class OrganizationalUnitDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('parent_name', fn (OrganizationalUnit $unit) => $unit->parent?->name ?? '-')
            ->addColumn('status_badge', function (OrganizationalUnit $unit) {
                if ($unit->is_active) {
                    return '<span class="badge badge-pill badge-pill--success">Aktif</span>';
                }

                return '<span class="badge badge-pill badge-pill--secondary">Nonaktif</span>';
            })
            ->addColumn('action', function (OrganizationalUnit $unit) {
                return view('partials.datatables.organizational-unit-actions', compact('unit'))->render();
            })
            ->filterColumn('parent_name', function ($query, $keyword) {
                $query->whereHas('parent', fn ($q) => $q->where('name', 'like', "%{$keyword}%"));
            })
            ->rawColumns(['status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return OrganizationalUnit::query()
            ->with('parent')
            ->withCount('employees');
    }
}
