<?php

namespace App\DataTables\Reports;

use App\Models\PayrollEntry;
use App\Models\User;
use App\Services\Reports\ReportScopeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class PayrollSummaryDataTable
{
    public function __construct(
        protected User $user,
        protected ReportScopeService $scope,
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_display', function (PayrollEntry $entry) {
                return e($entry->employee->employee_code).' — '.e($entry->employee->name);
            })
            ->addColumn('unit_display', fn (PayrollEntry $entry) => e($entry->employee->organizationalUnit?->name ?? '-'))
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
                    return '<span class="badge badge-pill badge-pill--secondary">Dilewati</span>';
                }

                return '<span class="badge badge-pill badge-pill--success">Diproses</span>';
            })
            ->filterColumn('employee_display', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('employee_code', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('unit_display', function ($query, $keyword) {
                $query->whereHas('employee.organizationalUnit', fn ($q) => $q->where('name', 'like', "%{$keyword}%"));
            })
            ->rawColumns(['net_display', 'status_badge'])
            ->toJson();
    }

    protected function query(): Builder
    {
        $periodId = (int) request('payroll_period_id');

        $query = PayrollEntry::query()
            ->with(['employee.organizationalUnit'])
            ->when($periodId, fn ($q) => $q->where('payroll_period_id', $periodId))
            ->when(! $periodId, fn ($q) => $q->whereRaw('1 = 0'))
            ->orderByDesc('net_salary');

        $this->scope->applyEmployeeScope($query, $this->user);

        return $query;
    }
}
