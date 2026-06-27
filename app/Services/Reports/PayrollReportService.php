<?php

namespace App\Services\Reports;

use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PayrollReportService
{
    public function __construct(
        protected ReportScopeService $scope
    ) {}

    /**
     * @return Collection<int, PayrollPeriod>
     */
    public function finalizedPeriods(): Collection
    {
        return PayrollPeriod::query()
            ->where('status', PayrollPeriod::STATUS_FINALIZED)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->get();
    }

    public function defaultPeriodId(): ?int
    {
        return $this->finalizedPeriods()->first()?->id;
    }

    /**
     * @return array{processed: int, total_earnings: float, total_deductions: float, net_salary: float}
     */
    public function summary(User $user, ?int $periodId = null): array
    {
        $periodId = $periodId ?? (int) request('payroll_period_id') ?: $this->defaultPeriodId();

        if (! $periodId) {
            return [
                'processed' => 0,
                'total_earnings' => 0,
                'total_deductions' => 0,
                'net_salary' => 0,
            ];
        }

        $query = PayrollEntry::query()
            ->where('payroll_period_id', $periodId)
            ->where('is_skipped', false);

        $this->scope->applyEmployeeScope($query, $user);

        return [
            'processed' => (int) $query->count(),
            'total_earnings' => (float) (clone $query)->sum('total_earnings'),
            'total_deductions' => (float) (clone $query)->sum('total_deductions'),
            'net_salary' => (float) (clone $query)->sum('net_salary'),
        ];
    }
}
