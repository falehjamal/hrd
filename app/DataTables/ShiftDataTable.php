<?php

namespace App\DataTables;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ShiftDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('work_hours', function (Shift $shift) {
                return Str::substr($shift->start_time, 0, 5).' - '.Str::substr($shift->end_time, 0, 5);
            })
            ->addColumn('break_display', fn (Shift $shift) => $shift->break_minutes.' menit')
            ->addColumn('status_badge', function (Shift $shift) {
                if ($shift->is_active) {
                    return '<span class="badge badge-pill badge-pill--success">Aktif</span>';
                }

                return '<span class="badge badge-pill badge-pill--secondary">Nonaktif</span>';
            })
            ->addColumn('action', function (Shift $shift) {
                return view('partials.datatables.shift-actions', compact('shift'))->render();
            })
            ->rawColumns(['status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return Shift::query()->withCount('employees');
    }
}
