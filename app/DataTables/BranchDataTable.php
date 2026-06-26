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
            ->addColumn('code_badge', fn (Branch $branch) => '<span class="badge badge-pill badge-pill--primary branch-code-badge">'.e($branch->code).'</span>')
            ->addColumn('name_display', fn (Branch $branch) => '<span class="fw-semibold">'.e($branch->name).'</span>')
            ->addColumn('location_display', function (Branch $branch) {
                $parts = array_filter([$branch->city, $branch->address]);
                $text = $parts !== [] ? implode(' · ', $parts) : '-';

                return '<span class="branch-location-cell"><i class="bx bx-map-pin text-muted me-1"></i>'.e($text).'</span>';
            })
            ->addColumn('head_office_badge', function (Branch $branch) {
                if ($branch->is_head_office) {
                    return '<span class="badge badge-pill badge-pill--primary branch-hq-badge">Kantor Pusat</span>';
                }

                return '<span class="text-muted">—</span>';
            })
            ->addColumn('status_badge', function (Branch $branch) {
                if ($branch->is_active) {
                    return '<span class="branch-status-badge branch-status-badge--active"><span class="branch-status-dot"></span>Aktif</span>';
                }

                return '<span class="branch-status-badge branch-status-badge--inactive"><span class="branch-status-dot"></span>Nonaktif</span>';
            })
            ->addColumn('action', function (Branch $branch) {
                return view('partials.datatables.branch-actions', compact('branch'))->render();
            })
            ->filterColumn('location_display', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('city', 'like', "%{$keyword}%")
                        ->orWhere('address', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['code_badge', 'name_display', 'location_display', 'head_office_badge', 'status_badge', 'action'])
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
