<?php

namespace App\DataTables;

use App\Models\EmployeeShiftOverride;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ShiftOverrideDataTable
{
    public function __construct(
        protected ?int $employeeId = null
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_display', function (EmployeeShiftOverride $row) {
                $emp = $row->employee;

                return $emp
                    ? '<a href="'.route('employees.show', $emp).'">'.e($emp->employee_code).'</a> — '.e($emp->name)
                    : '-';
            })
            ->addColumn('date_display', fn (EmployeeShiftOverride $row) => $row->date->format('d/m/Y'))
            ->addColumn('shift_display', function (EmployeeShiftOverride $row) {
                if ($row->shift_id === null) {
                    return '<span class="badge bg-label-secondary">Libur</span>';
                }

                return e($row->shift->code.' - '.$row->shift->name);
            })
            ->addColumn('action', function (EmployeeShiftOverride $row) {
                return view('partials.datatables.shift-override-actions', ['override' => $row])->render();
            })
            ->filterColumn('employee_display', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('employee_code', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['employee_display', 'shift_display', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return EmployeeShiftOverride::query()
            ->with(['employee', 'shift'])
            ->when($this->employeeId, fn ($q) => $q->where('employee_id', $this->employeeId))
            ->when(request()->filled('employee_id'), fn ($q) => $q->where('employee_id', request('employee_id')))
            ->when(request()->filled('date_from'), fn ($q) => $q->whereDate('date', '>=', request('date_from')))
            ->when(request()->filled('date_to'), fn ($q) => $q->whereDate('date', '<=', request('date_to')))
            ->orderByDesc('date')
            ->orderByDesc('id');
    }
}
