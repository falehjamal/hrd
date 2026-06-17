<?php

namespace App\DataTables;

use App\Models\PayrollEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class PayrollEntryDataTable
{
    public function __construct(
        protected int $payrollPeriodId,
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_name', function (PayrollEntry $entry) {
                return '<a href="'.route('employees.show', $entry->employee).'" class="fw-medium">'.$entry->employee->name.'</a>';
            })
            ->addColumn('employee_code', fn (PayrollEntry $entry) => $entry->employee->employee_code)
            ->addColumn('earnings_display', fn (PayrollEntry $entry) => $entry->is_skipped ? '—' : format_rupiah($entry->total_earnings))
            ->addColumn('deductions_display', fn (PayrollEntry $entry) => $entry->is_skipped ? '—' : format_rupiah($entry->total_deductions))
            ->addColumn('net_display', function (PayrollEntry $entry) {
                if ($entry->is_skipped) {
                    return '<span class="text-muted">'.e($entry->skip_reason).'</span>';
                }

                return '<strong>'.format_rupiah($entry->net_salary).'</strong>';
            })
            ->addColumn('status_badge', function (PayrollEntry $entry) {
                if ($entry->is_skipped) {
                    return '<span class="badge bg-label-secondary">Dilewati</span>';
                }

                return '<span class="badge bg-label-success">Diproses</span>';
            })
            ->addColumn('action', fn (PayrollEntry $entry) => view('partials.datatables.payroll-entry-actions', [
                'entry' => $entry,
                'period' => $entry->period,
            ])->render())
            ->filterColumn('employee_name', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('employee_code', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['employee_name', 'net_display', 'status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return PayrollEntry::query()
            ->with(['employee', 'period'])
            ->where('payroll_period_id', $this->payrollPeriodId);
    }
}
