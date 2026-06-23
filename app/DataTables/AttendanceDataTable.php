<?php

namespace App\DataTables;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class AttendanceDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_display', function (Attendance $row) {
                $emp = $row->employee;

                return $emp
                    ? '<a href="'.route('employees.show', $emp).'">'.e($emp->employee_code).'</a> — '.e($emp->name)
                    : '-';
            })
            ->addColumn('date_display', fn (Attendance $row) => $row->date->format('d/m/Y'))
            ->addColumn('check_in_display', fn (Attendance $row) => $row->check_in_at?->format('H:i') ?? '-')
            ->addColumn('check_out_display', fn (Attendance $row) => $row->check_out_at?->format('H:i') ?? '-')
            ->addColumn('shift_display', function (Attendance $row) {
                if ($row->shift) {
                    return e($row->shift->code);
                }

                return '-';
            })
            ->addColumn('source_badge', function (Attendance $row) {
                $label = $row->source === Attendance::SOURCE_GPS ? 'GPS' : 'Manual';

                return '<span class="badge badge-pill badge-pill--info">'.e($label).'</span>';
            })
            ->addColumn('status_badge', function (Attendance $row) {
                $colors = [
                    Attendance::STATUS_PRESENT => 'success',
                    Attendance::STATUS_LATE => 'warning',
                    Attendance::STATUS_ABSENT => 'danger',
                    Attendance::STATUS_HALF_DAY => 'info',
                    Attendance::STATUS_LEAVE => 'secondary',
                ];
                $color = $colors[$row->status] ?? 'secondary';

                return status_badge_html(attendance_status_label($row->status), $color);
            })
            ->addColumn('photo_links', function (Attendance $row) {
                $links = [];
                if ($row->check_in_photo_path) {
                    $links[] = '<a href="'.route('attendances.photo', [$row, 'check-in']).'" target="_blank" class="btn btn-xs btn-outline-primary btn-sm py-0">Masuk</a>';
                }
                if ($row->check_out_photo_path) {
                    $links[] = '<a href="'.route('attendances.photo', [$row, 'check-out']).'" target="_blank" class="btn btn-xs btn-outline-secondary btn-sm py-0">Pulang</a>';
                }

                return $links ? implode(' ', $links) : '-';
            })
            ->addColumn('action', function (Attendance $row) {
                return view('partials.datatables.attendance-actions', ['attendance' => $row])->render();
            })
            ->filterColumn('employee_display', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('employee_code', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['employee_display', 'source_badge', 'status_badge', 'photo_links', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return Attendance::query()
            ->with(['employee', 'shift'])
            ->when(request()->filled('date_from'), fn ($q) => $q->whereDate('date', '>=', request('date_from')))
            ->when(request()->filled('date_to'), fn ($q) => $q->whereDate('date', '<=', request('date_to')))
            ->when(request()->filled('status'), fn ($q) => $q->where('status', request('status')))
            ->when(request()->filled('employee_id'), fn ($q) => $q->where('employee_id', request('employee_id')))
            ->orderByDesc('date')
            ->orderByDesc('id');
    }
}
