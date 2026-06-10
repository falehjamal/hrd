<?php

namespace App\DataTables;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class EmployeeDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('name_link', function (Employee $employee) {
                return '<a href="'.route('employees.show', $employee).'" class="fw-medium">'.$employee->name.'</a>';
            })
            ->addColumn('unit_name', fn (Employee $employee) => $employee->organizationalUnit?->name ?? '-')
            ->addColumn('position_name', fn (Employee $employee) => $employee->position?->name ?? '-')
            ->addColumn('shift_code', fn (Employee $employee) => $employee->shift?->code ?? '-')
            ->addColumn('salary_display', function (Employee $employee) {
                if ($employee->activeSalary) {
                    return format_rupiah($employee->activeSalary->total_salary);
                }

                return '<span class="text-muted">-</span>';
            })
            ->addColumn('status_badge', function (Employee $employee) {
                if ($employee->status === 'active') {
                    return '<span class="badge bg-label-success">Aktif</span>';
                }

                return '<span class="badge bg-label-secondary">Nonaktif</span>';
            })
            ->addColumn('action', function (Employee $employee) {
                return view('partials.datatables.employee-actions', compact('employee'))->render();
            })
            ->filterColumn('name_link', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('employees.name', 'like', "%{$keyword}%")
                        ->orWhere('employees.employee_code', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['name_link', 'salary_display', 'status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return Employee::query()
            ->with(['shift', 'activeSalary', 'position', 'organizationalUnit'])
            ->when(request()->filled('status'), fn ($q) => $q->where('status', request('status')));
    }
}
