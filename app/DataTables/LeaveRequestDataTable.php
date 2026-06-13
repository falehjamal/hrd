<?php

namespace App\DataTables;

use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class LeaveRequestDataTable
{
    public function __construct(
        protected ?int $employeeId = null
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_display', function (LeaveRequest $row) {
                $emp = $row->employee;

                return $emp ? e($emp->employee_code).' — '.e($emp->name) : '-';
            })
            ->addColumn('leave_type_display', fn (LeaveRequest $row) => e($row->leaveType?->code ?? '-').' — '.e($row->leaveType?->name ?? '-'))
            ->addColumn('date_range', fn (LeaveRequest $row) => $row->start_date->format('d/m/Y').' — '.$row->end_date->format('d/m/Y'))
            ->addColumn('total_days_display', fn (LeaveRequest $row) => $row->total_days.' hari')
            ->addColumn('status_badge', function (LeaveRequest $row) {
                $colors = [
                    LeaveRequest::STATUS_PENDING => 'warning',
                    LeaveRequest::STATUS_APPROVED => 'success',
                    LeaveRequest::STATUS_REJECTED => 'danger',
                ];
                $color = $colors[$row->status] ?? 'secondary';

                return '<span class="badge bg-label-'.$color.'">'.e(leave_status_label($row->status)).'</span>';
            })
            ->addColumn('action', function (LeaveRequest $row) {
                return view('partials.datatables.leave-request-actions', ['leaveRequest' => $row])->render();
            })
            ->filterColumn('employee_display', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('employee_code', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('leave_type_display', function ($query, $keyword) {
                $query->whereHas('leaveType', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('code', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return LeaveRequest::query()
            ->with(['employee', 'leaveType'])
            ->when($this->employeeId, fn ($q) => $q->where('employee_id', $this->employeeId))
            ->when(request()->filled('status'), fn ($q) => $q->where('status', request('status')))
            ->when(request()->filled('leave_type_id'), fn ($q) => $q->where('leave_type_id', request('leave_type_id')))
            ->when(request()->filled('date_from'), fn ($q) => $q->whereDate('end_date', '>=', request('date_from')))
            ->when(request()->filled('date_to'), fn ($q) => $q->whereDate('start_date', '<=', request('date_to')))
            ->orderByDesc('start_date')
            ->orderByDesc('id');
    }
}
