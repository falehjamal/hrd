<?php

namespace App\DataTables;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class LeaveTypeDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('paid_badge', function (LeaveType $type) {
                if ($type->is_paid) {
                    return '<span class="badge bg-label-success">Berbayar</span>';
                }

                return '<span class="badge bg-label-secondary">Tidak Berbayar</span>';
            })
            ->addColumn('status_badge', function (LeaveType $type) {
                if ($type->is_active) {
                    return '<span class="badge bg-label-success">Aktif</span>';
                }

                return '<span class="badge bg-label-secondary">Nonaktif</span>';
            })
            ->addColumn('action', function (LeaveType $type) {
                return view('partials.datatables.leave-type-actions', compact('type'))->render();
            })
            ->rawColumns(['paid_badge', 'status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return LeaveType::query()->orderBy('code');
    }
}
