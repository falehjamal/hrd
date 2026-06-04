<?php

namespace App\DataTables;

use App\Models\OvertimeRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class OvertimeRequestDataTable
{
    public function __construct(
        protected ?int $employeeId = null
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_display', function (OvertimeRequest $row) {
                $emp = $row->employee;

                return $emp ? e($emp->employee_code).' — '.e($emp->name) : '-';
            })
            ->addColumn('date_display', fn (OvertimeRequest $row) => $row->date->format('d/m/Y'))
            ->addColumn('time_range', fn (OvertimeRequest $row) => substr($row->start_time, 0, 5).' - '.substr($row->end_time, 0, 5))
            ->addColumn('duration_display', fn (OvertimeRequest $row) => $row->duration_minutes.' menit')
            ->addColumn('status_badge', function (OvertimeRequest $row) {
                $colors = [
                    OvertimeRequest::STATUS_PENDING => 'warning',
                    OvertimeRequest::STATUS_APPROVED => 'success',
                    OvertimeRequest::STATUS_REJECTED => 'danger',
                ];
                $color = $colors[$row->status] ?? 'secondary';

                return '<span class="badge bg-label-'.$color.'">'.e(overtime_status_label($row->status)).'</span>';
            })
            ->addColumn('action', function (OvertimeRequest $row) {
                return view('partials.datatables.overtime-actions', ['overtime' => $row])->render();
            })
            ->filterColumn('employee_display', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('employee_code', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return OvertimeRequest::query()
            ->with('employee')
            ->when($this->employeeId, fn ($q) => $q->where('employee_id', $this->employeeId))
            ->when(request()->filled('status'), fn ($q) => $q->where('status', request('status')))
            ->when(request()->filled('date_from'), fn ($q) => $q->whereDate('date', '>=', request('date_from')))
            ->when(request()->filled('date_to'), fn ($q) => $q->whereDate('date', '<=', request('date_to')))
            ->orderByDesc('date')
            ->orderByDesc('id');
    }
}
