<?php

namespace App\DataTables;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class BranchDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('location_display', function (Branch $branch) {
                if ($branch->city) {
                    return $branch->city;
                }

                return $branch->address ?? '-';
            })
            ->addColumn('head_office_badge', function (Branch $branch) {
                if ($branch->is_head_office) {
                    return '<span class="badge badge-pill badge-pill--primary">Kantor Pusat</span>';
                }

                return '';
            })
            ->addColumn('status_badge', function (Branch $branch) {
                if ($branch->is_active) {
                    return '<span class="badge badge-pill badge-pill--success">Aktif</span>';
                }

                return '<span class="badge badge-pill badge-pill--secondary">Nonaktif</span>';
            })
            ->addColumn('action', function (Branch $branch) {
                return view('partials.datatables.branch-actions', compact('branch'))->render();
            })
            ->rawColumns(['head_office_badge', 'status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return Branch::query()
            ->withCount('employees')
            ->orderByDesc('is_head_office')
            ->orderBy('name');
    }
}
