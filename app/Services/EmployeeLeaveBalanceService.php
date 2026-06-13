<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeWeeklyShift;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class EmployeeLeaveBalanceService
{
    public function __construct(
        protected EmployeeShiftResolverService $shiftResolver
    ) {}

    public function ensureBalancesForYear(Employee $employee, int $year): Collection
    {
        $types = LeaveType::query()->active()->orderBy('code')->get();

        foreach ($types as $type) {
            EmployeeLeaveBalance::query()->firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'leave_type_id' => $type->id,
                    'year' => $year,
                ],
                [
                    'quota_days' => $type->default_quota_days,
                    'used_days' => 0,
                ]
            );
        }

        return EmployeeLeaveBalance::query()
            ->with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('year', $year)
            ->join('leave_types', 'leave_types.id', '=', 'employee_leave_balances.leave_type_id')
            ->orderBy('leave_types.code')
            ->select('employee_leave_balances.*')
            ->get();
    }

    public function syncUsedDays(EmployeeLeaveBalance $balance): void
    {
        $usedDays = $this->calculateUsedDays(
            $balance->employee_id,
            $balance->leave_type_id,
            $balance->year
        );

        $balance->update(['used_days' => $usedDays]);
    }

    public function syncAllUsedDaysForEmployee(Employee $employee, int $year): void
    {
        $balances = EmployeeLeaveBalance::query()
            ->where('employee_id', $employee->id)
            ->where('year', $year)
            ->get();

        foreach ($balances as $balance) {
            $this->syncUsedDays($balance);
        }
    }

    public function updateQuotas(Employee $employee, int $year, array $items): void
    {
        foreach ($items as $item) {
            $leaveTypeId = (int) $item['leave_type_id'];
            $quotaDays = (int) $item['quota_days'];

            $balance = EmployeeLeaveBalance::query()->firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveTypeId,
                    'year' => $year,
                ],
                [
                    'quota_days' => $quotaDays,
                    'used_days' => 0,
                ]
            );

            $balance->update(['quota_days' => $quotaDays]);
            $this->syncUsedDays($balance);
        }
    }

    public function getBalance(Employee $employee, int $leaveTypeId, int $year): ?EmployeeLeaveBalance
    {
        return EmployeeLeaveBalance::query()
            ->where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();
    }

    public function getOrCreateBalance(Employee $employee, int $leaveTypeId, int $year): EmployeeLeaveBalance
    {
        $type = LeaveType::query()->findOrFail($leaveTypeId);

        $balance = EmployeeLeaveBalance::query()->firstOrCreate(
            [
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveTypeId,
                'year' => $year,
            ],
            [
                'quota_days' => $type->default_quota_days,
                'used_days' => 0,
            ]
        );

        $this->syncUsedDays($balance);

        return $balance->fresh('leaveType');
    }

    protected function calculateUsedDays(int $employeeId, int $leaveTypeId, int $year): int
    {
        $employee = Employee::query()->find($employeeId);

        if (! $employee) {
            return 0;
        }

        $total = 0;

        LeaveRequest::query()
            ->where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->where(function ($query) use ($year) {
                $query->whereYear('start_date', $year)
                    ->orWhereYear('end_date', $year);
            })
            ->get()
            ->each(function (LeaveRequest $request) use ($employee, $year, &$total) {
                $total += $this->countWorkingDaysInYear($employee, $request->start_date, $request->end_date, $year);
            });

        return $total;
    }

    protected function countWorkingDaysInYear(Employee $employee, Carbon|string $startDate, Carbon|string $endDate, int $year): int
    {
        return $this->workingDatesInRange($employee, $startDate, $endDate)
            ->filter(fn (Carbon $date) => $date->year === $year)
            ->count();
    }

    protected function workingDatesInRange(Employee $employee, Carbon|string $startDate, Carbon|string $endDate): Collection
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($end->lessThan($start)) {
            return collect();
        }

        $dates = collect();

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($this->isWorkingDay($employee, $date)) {
                $dates->push($date->copy());
            }
        }

        return $dates;
    }

    protected function isWorkingDay(Employee $employee, Carbon $date): bool
    {
        if ($this->shiftResolver->isCompanyHoliday($date)) {
            return false;
        }

        if ($this->shiftResolver->isDayOff($employee, $date)) {
            return false;
        }

        $weekly = EmployeeWeeklyShift::query()
            ->where('employee_id', $employee->id)
            ->where('day_of_week', $date->isoWeekday())
            ->first();

        if ($weekly && $weekly->shift_id === null) {
            return false;
        }

        return $this->shiftResolver->shiftForDate($employee, $date) !== null;
    }
}
