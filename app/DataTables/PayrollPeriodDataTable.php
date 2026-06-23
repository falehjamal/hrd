<?php

namespace App\DataTables;

use App\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class PayrollPeriodDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('period_display', fn (PayrollPeriod $period) => $period->periodLabel())
            ->addColumn('status_badge', function (PayrollPeriod $period) {
                if ($period->isFinalized()) {
                    return '<span class="badge badge-pill badge-pill--success">'.payroll_period_status_label($period->status).'</span>';
                }

                return '<span class="badge badge-pill badge-pill--warning">'.payroll_period_status_label($period->status).'</span>';
            })
            ->addColumn('entries_count', fn (PayrollPeriod $period) => (string) $period->entries_count)
            ->addColumn('total_net_display', fn (PayrollPeriod $period) => format_rupiah($period->entries_sum_net_salary ?? 0))
            ->addColumn('finalized_display', fn (PayrollPeriod $period) => $period->finalized_at?->format('d/m/Y H:i') ?? '—')
            ->addColumn('action', fn (PayrollPeriod $period) => view('partials.datatables.payroll-period-actions', compact('period'))->render())
            ->rawColumns(['status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return PayrollPeriod::query()
            ->withCount('entries')
            ->withSum(['entries as entries_sum_net_salary' => fn ($q) => $q->where('is_skipped', false)], 'net_salary')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month');
    }
}
