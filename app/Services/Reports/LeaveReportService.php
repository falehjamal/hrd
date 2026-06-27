<?php

namespace App\Services\Reports;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveReportService
{
    public function __construct(
        protected ReportScopeService $scope
    ) {}

    /**
     * @return array{total_requests: int, approved_days: int, pending: int}
     */
    public function summary(User $user): array
    {
        $year = (int) request('year', $this->defaultYear());

        $baseQuery = fn () => LeaveRequest::query()
            ->where(function ($q) use ($year) {
                $q->whereYear('start_date', $year)
                    ->orWhereYear('end_date', $year);
            })
            ->when(request()->filled('leave_type_id'), fn ($q) => $q->where('leave_type_id', request('leave_type_id')))
            ->when(request()->filled('branch_id'), fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->where('branch_id', request('branch_id'))))
            ->when(request()->filled('organizational_unit_id'), fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->where('organizational_unit_id', request('organizational_unit_id'))));

        $scoped = $baseQuery();
        $this->scope->applyEmployeeScope($scoped, $user);

        $filtered = clone $scoped;
        if (request()->filled('status')) {
            $filtered->where('status', request('status'));
        }

        $approvedQuery = clone $scoped;
        $approvedQuery->where('status', LeaveRequest::STATUS_APPROVED);

        $pendingQuery = clone $scoped;
        $pendingQuery->where('status', LeaveRequest::STATUS_PENDING);

        return [
            'total_requests' => (int) $filtered->count(),
            'approved_days' => (int) $approvedQuery->sum('total_days'),
            'pending' => (int) $pendingQuery->count(),
        ];
    }

    public function defaultYear(): int
    {
        return (int) now()->year;
    }
}
