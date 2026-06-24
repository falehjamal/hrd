<?php

namespace App\DataTables;

use App\Models\WorkLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class WorkLocationDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('branch_name', fn (WorkLocation $loc) => $loc->branch?->name ?? 'Global')
            ->addColumn('coordinates', fn (WorkLocation $loc) => $loc->latitude.', '.$loc->longitude)
            ->addColumn('radius_display', fn (WorkLocation $loc) => $loc->radius_meters.' m')
            ->addColumn('default_badge', function (WorkLocation $loc) {
                if ($loc->is_default) {
                    return '<span class="badge badge-pill badge-pill--primary">Default</span>';
                }

                return '';
            })
            ->addColumn('status_badge', function (WorkLocation $loc) {
                if ($loc->is_active) {
                    return '<span class="badge badge-pill badge-pill--success">Aktif</span>';
                }

                return '<span class="badge badge-pill badge-pill--secondary">Nonaktif</span>';
            })
            ->addColumn('action', function (WorkLocation $loc) {
                return view('partials.datatables.work-location-actions', compact('loc'))->render();
            })
            ->rawColumns(['default_badge', 'status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return WorkLocation::query()
            ->with('branch')
            ->orderByDesc('is_default')
            ->orderBy('name');
    }
}
