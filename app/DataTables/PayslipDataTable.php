<?php

namespace App\DataTables;

use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class PayslipDataTable
{
    public function __construct(
        protected int $employeeId,
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('period_display', fn (PayrollEntry $entry) => $entry->period->periodLabel())
            ->addColumn('earnings_display', fn (PayrollEntry $entry) => format_rupiah($entry->total_earnings))
            ->addColumn('deductions_display', fn (PayrollEntry $entry) => format_rupiah($entry->total_deductions))
            ->addColumn('net_display', fn (PayrollEntry $entry) => '<strong>'.format_rupiah($entry->net_salary).'</strong>')
            ->addColumn('finalized_display', fn (PayrollEntry $entry) => $entry->period->finalized_at?->format('d/m/Y') ?? '—')
            ->addColumn('action', fn (PayrollEntry $entry) => view('partials.datatables.payslip-actions', compact('entry'))->render())
            ->rawColumns(['net_display', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return PayrollEntry::query()
            ->with('period')
            ->where('employee_id', $this->employeeId)
            ->where('is_skipped', false)
            ->whereHas('period', fn ($q) => $q->where('status', PayrollPeriod::STATUS_FINALIZED))
            ->join('payroll_periods', 'payroll_entries.payroll_period_id', '=', 'payroll_periods.id')
            ->select('payroll_entries.*')
            ->orderByDesc('payroll_periods.period_year')
            ->orderByDesc('payroll_periods.period_month');
    }
}
