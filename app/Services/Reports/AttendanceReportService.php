<?php

namespace App\Services\Reports;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AttendanceReportService
{
    public function __construct(
        protected ReportScopeService $scope
    ) {}

    /**
     * @return array{present: int, late: int, absent: int, leave: int, half_day: int, total: int}
     */
    public function summary(User $user): array
    {
        $dateFrom = request('date_from', $this->defaultDateFrom());
        $dateTo = request('date_to', $this->defaultDateTo());

        $query = Attendance::query()
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo)
            ->when(request()->filled('branch_id'), fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->where('branch_id', request('branch_id'))))
            ->when(request()->filled('organizational_unit_id'), fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->where('organizational_unit_id', request('organizational_unit_id'))));

        $this->scope->applyEmployeeScope($query, $user);

        $counts = $query
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $present = (int) ($counts[Attendance::STATUS_PRESENT] ?? 0);
        $late = (int) ($counts[Attendance::STATUS_LATE] ?? 0);
        $absent = (int) ($counts[Attendance::STATUS_ABSENT] ?? 0);
        $leave = (int) ($counts[Attendance::STATUS_LEAVE] ?? 0);
        $halfDay = (int) ($counts[Attendance::STATUS_HALF_DAY] ?? 0);

        return [
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'leave' => $leave,
            'half_day' => $halfDay,
            'total' => $present + $late + $absent + $leave + $halfDay,
        ];
    }

    public function defaultDateFrom(): string
    {
        return now()->startOfMonth()->format('Y-m-d');
    }

    public function defaultDateTo(): string
    {
        return now()->endOfMonth()->format('Y-m-d');
    }
}
