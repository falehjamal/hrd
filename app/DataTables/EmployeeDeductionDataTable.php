<?php

namespace App\DataTables;

use App\Models\EmployeeDeduction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class EmployeeDeductionDataTable
{
    public function __construct(
        protected ?int $employeeId = null,
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_name', function (EmployeeDeduction $deduction) {
                return '<a href="'.route('employees.show', $deduction->employee).'" class="fw-medium">'.$deduction->employee->name.'</a>';
            })
            ->addColumn('employee_code', fn (EmployeeDeduction $deduction) => $deduction->employee->employee_code)
            ->addColumn('type_display', fn (EmployeeDeduction $deduction) => e($deduction->deductionType->code).' — '.e($deduction->deductionType->name))
            ->addColumn('amount_display', fn (EmployeeDeduction $deduction) => format_rupiah($deduction->amount))
            ->addColumn('effective_date_display', fn (EmployeeDeduction $deduction) => $deduction->effective_date->format('d/m/Y'))
            ->addColumn('status_badge', function (EmployeeDeduction $deduction) {
                if ($deduction->is_active) {
                    return '<span class="badge bg-label-success">Aktif</span>';
                }

                return '<span class="badge bg-label-secondary">Arsip</span>';
            })
            ->addColumn('action', function (EmployeeDeduction $deduction) {
                $view = $this->employeeId
                    ? 'partials.datatables.deduction-row-actions'
                    : 'partials.datatables.deduction-actions';

                return view($view, compact('deduction'))->render();
            })
            ->filterColumn('employee_name', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('employee_code', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('type_display', function ($query, $keyword) {
                $query->whereHas('deductionType', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('code', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['employee_name', 'status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return EmployeeDeduction::query()
            ->with(['employee', 'deductionType'])
            ->when($this->employeeId, fn ($q) => $q->where('employee_id', $this->employeeId))
            ->when(
                ! $this->employeeId && request()->boolean('active_only', true),
                fn ($q) => $q->where('is_active', true)
            );
    }
}
