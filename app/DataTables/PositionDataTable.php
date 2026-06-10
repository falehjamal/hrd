<?php

namespace App\DataTables;

use App\Models\Position;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class PositionDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('status_badge', function (Position $position) {
                if ($position->is_active) {
                    return '<span class="badge bg-label-success">Aktif</span>';
                }

                return '<span class="badge bg-label-secondary">Nonaktif</span>';
            })
            ->addColumn('action', function (Position $position) {
                return view('partials.datatables.position-actions', compact('position'))->render();
            })
            ->rawColumns(['status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return Position::query()->withCount('employees');
    }
}
