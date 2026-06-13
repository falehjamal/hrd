<?php

namespace App\DataTables;

use App\Models\EmployeeLoan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class EmployeeLoanDataTable
{
    public function __construct(
        protected ?int $employeeId = null,
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_display', function (EmployeeLoan $loan) {
                return '<a href="'.route('employees.show', $loan->employee).'" class="fw-medium">'.e($loan->employee->employee_code).' — '.e($loan->employee->name).'</a>';
            })
            ->addColumn('loan_date_display', fn (EmployeeLoan $loan) => $loan->loan_date->format('d/m/Y'))
            ->addColumn('principal_display', fn (EmployeeLoan $loan) => format_rupiah($loan->principal_amount))
            ->addColumn('installment_display', fn (EmployeeLoan $loan) => format_rupiah($loan->installment_amount))
            ->addColumn('remaining_display', fn (EmployeeLoan $loan) => format_rupiah($loan->remaining_amount))
            ->addColumn('progress_display', fn (EmployeeLoan $loan) => $loan->total_installments.' cicilan')
            ->addColumn('status_badge', function (EmployeeLoan $loan) {
                $colors = [
                    EmployeeLoan::STATUS_ACTIVE => 'warning',
                    EmployeeLoan::STATUS_PAID => 'success',
                    EmployeeLoan::STATUS_CANCELLED => 'secondary',
                ];
                $color = $colors[$loan->status] ?? 'secondary';

                return '<span class="badge bg-label-'.$color.'">'.e(loan_status_label($loan->status)).'</span>';
            })
            ->addColumn('action', function (EmployeeLoan $loan) {
                return view('partials.datatables.employee-loan-actions', compact('loan'))->render();
            })
            ->filterColumn('employee_display', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('employee_code', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['employee_display', 'status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return EmployeeLoan::query()
            ->with('employee')
            ->when($this->employeeId, fn ($q) => $q->where('employee_id', $this->employeeId))
            ->when(request()->filled('status'), fn ($q) => $q->where('status', request('status')))
            ->orderByDesc('loan_date')
            ->orderByDesc('id');
    }
}
