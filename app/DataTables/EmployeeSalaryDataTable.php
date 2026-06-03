<?php

namespace App\DataTables;

use App\Models\EmployeeSalary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class EmployeeSalaryDataTable
{
    public function __construct(
        protected ?int $employeeId = null,
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_name', function (EmployeeSalary $salary) {
                return '<a href="'.route('employees.show', $salary->employee).'" class="fw-medium">'.$salary->employee->name.'</a>';
            })
            ->addColumn('employee_code', fn (EmployeeSalary $salary) => $salary->employee->employee_code)
            ->addColumn('effective_date_display', fn (EmployeeSalary $salary) => $salary->effective_date->format('d/m/Y'))
            ->addColumn('basic_display', fn (EmployeeSalary $salary) => format_rupiah($salary->basic_salary))
            ->addColumn('allowance_display', fn (EmployeeSalary $salary) => format_rupiah($salary->fixed_allowance))
            ->addColumn('total_display', fn (EmployeeSalary $salary) => '<strong>'.format_rupiah($salary->total_salary).'</strong>')
            ->addColumn('status_badge', function (EmployeeSalary $salary) {
                if ($salary->is_active) {
                    return '<span class="badge bg-label-success">Aktif</span>';
                }

                return '<span class="badge bg-label-secondary">Arsip</span>';
            })
            ->addColumn('action', function (EmployeeSalary $salary) {
                $view = $this->employeeId
                    ? 'partials.datatables.salary-row-actions'
                    : 'partials.datatables.salary-actions';

                return view($view, compact('salary'))->render();
            })
            ->filterColumn('employee_name', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('employee_code', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['employee_name', 'total_display', 'status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return EmployeeSalary::query()
            ->with('employee')
            ->when($this->employeeId, fn ($q) => $q->where('employee_id', $this->employeeId))
            ->when(
                ! $this->employeeId && request()->boolean('active_only', true),
                fn ($q) => $q->where('is_active', true)
            );
    }
}
