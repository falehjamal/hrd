<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CompanyHoliday;
use App\Models\Employee;
use App\Models\EmployeeShiftOverride;
use Carbon\Carbon;

class ShiftCalendarService
{
    public function __construct(
        protected EmployeeShiftResolverService $resolver
    ) {}

    public function buildMonth(int $year, int $month, ?int $employeeId = null): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $cells = $employeeId
            ? $this->buildEmployeeCells($employeeId, $start, $end)
            : $this->buildOverviewCells($start, $end);

        return [
            'year' => $year,
            'month' => $month,
            'month_label' => $start->translatedFormat('F Y'),
            'employee_id' => $employeeId,
            'weekday_headers' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            'leading_blanks' => $start->isoWeekday() - 1,
            'days' => $cells,
            'legend' => $this->legend(),
        ];
    }

    public function dayDetail(string $date, ?int $employeeId = null): array
    {
        $carbon = Carbon::parse($date)->startOfDay();

        if ($employeeId) {
            return $this->employeeDayDetail($employeeId, $carbon);
        }

        return $this->overviewDayDetail($carbon);
    }

    protected function buildOverviewCells(Carbon $start, Carbon $end): array
    {
        $holidays = CompanyHoliday::query()
            ->active()
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn ($h) => $h->date->format('Y-m-d'));

        $overrides = EmployeeShiftOverride::query()
            ->with(['employee', 'shift'])
            ->whereBetween('date', [$start, $end])
            ->get()
            ->groupBy(fn ($o) => $o->date->format('Y-m-d'));

        $leaves = Attendance::query()
            ->with('employee')
            ->whereBetween('date', [$start, $end])
            ->where('status', Attendance::STATUS_LEAVE)
            ->get()
            ->groupBy(fn ($a) => $a->date->format('Y-m-d'));

        $days = [];

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $dayOverrides = $overrides->get($key, collect());
            $liburCount = $dayOverrides->whereNull('shift_id')->count();
            $gantiCount = $dayOverrides->whereNotNull('shift_id')->count();
            $cutiCount = $leaves->get($key, collect())->count();
            $holiday = $holidays->get($key);

            $badgeParts = [];
            if ($holiday) {
                $badgeParts[] = 'Libur PT';
            }
            if ($liburCount > 0) {
                $badgeParts[] = $liburCount.' libur';
            }
            if ($cutiCount > 0) {
                $badgeParts[] = $cutiCount.' cuti';
            }
            if ($gantiCount > 0) {
                $badgeParts[] = $gantiCount.' ganti';
            }

            $cellClass = 'shift-cal-day--empty';
            if ($holiday) {
                $cellClass = 'shift-cal-day--company';
            } elseif ($liburCount > 0 || $cutiCount > 0) {
                $cellClass = 'shift-cal-day--off';
            } elseif ($gantiCount > 0) {
                $cellClass = 'shift-cal-day--override';
            }

            $days[] = [
                'date' => $key,
                'day' => (int) $d->format('j'),
                'is_company_holiday' => $holiday !== null,
                'company_holiday_name' => $holiday?->name,
                'libur_count' => $liburCount,
                'ganti_shift_count' => $gantiCount,
                'cuti_count' => $cutiCount,
                'badge' => $badgeParts ? implode(' · ', $badgeParts) : '',
                'cell_class' => $cellClass,
                'is_current_month' => true,
            ];
        }

        return $days;
    }

    protected function buildEmployeeCells(int $employeeId, Carbon $start, Carbon $end): array
    {
        $employee = Employee::query()->with(['shift', 'weeklyShifts.shift'])->findOrFail($employeeId);

        $holidays = CompanyHoliday::query()
            ->active()
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn ($h) => $h->date->format('Y-m-d'));

        $overrides = EmployeeShiftOverride::query()
            ->with('shift')
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn ($o) => $o->date->format('Y-m-d'));

        $attendances = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn ($a) => $a->date->format('Y-m-d'));

        $days = [];

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $override = $overrides->get($key);
            $holiday = $holidays->get($key);
            $attendance = $attendances->get($key);
            $shift = $this->resolver->shiftForDate($employee, $d);
            $source = $this->resolver->scheduleSource($employee, $d);

            $scheduleType = 'work';
            $cellClass = 'shift-cal-day--work';

            if ($override && $override->shift_id === null) {
                $scheduleType = 'libur';
                $cellClass = 'shift-cal-day--off';
            } elseif ($holiday && ! $override) {
                $scheduleType = 'libur_perusahaan';
                $cellClass = 'shift-cal-day--company';
            } elseif ($attendance?->status === Attendance::STATUS_LEAVE) {
                $scheduleType = 'cuti';
                $cellClass = 'shift-cal-day--leave';
            } elseif ($override && $override->shift_id) {
                $scheduleType = 'ganti_shift';
                $cellClass = 'shift-cal-day--override';
            } elseif (! $shift) {
                $scheduleType = 'libur';
                $cellClass = 'shift-cal-day--off';
            }

            $attendanceBorder = null;
            if ($attendance) {
                $attendanceBorder = match ($attendance->status) {
                    Attendance::STATUS_PRESENT => 'shift-cal-day--hadir',
                    Attendance::STATUS_LATE => 'shift-cal-day--late',
                    Attendance::STATUS_ABSENT => 'shift-cal-day--alpha',
                    Attendance::STATUS_LEAVE => 'shift-cal-day--leave',
                    default => null,
                };
            }

            $shiftTime = $shift
                ? substr($shift->start_time, 0, 5).'–'.substr($shift->end_time, 0, 5)
                : null;

            $days[] = [
                'date' => $key,
                'day' => (int) $d->format('j'),
                'schedule_type' => $scheduleType,
                'schedule_source' => $source,
                'shift_code' => $shift?->code,
                'shift_label' => $this->resolver->shiftLabelForDate($employee, $d),
                'shift_time' => $shiftTime,
                'attendance_status' => $attendance?->status,
                'attendance_label' => $attendance ? attendance_status_label($attendance->status) : null,
                'has_override' => $override !== null,
                'override_id' => $override?->id,
                'notes' => $override?->notes,
                'company_holiday_name' => $holiday?->name,
                'badge' => $shift?->code ?? ($holiday ? 'Libur PT' : ($scheduleType === 'libur' ? 'Libur' : ($scheduleType === 'cuti' ? 'Cuti' : ''))),
                'cell_class' => trim($cellClass.' '.($attendanceBorder ?? '')),
                'is_current_month' => true,
            ];
        }

        return $days;
    }

    protected function overviewDayDetail(Carbon $date): array
    {
        $key = $date->format('Y-m-d');
        $holiday = $this->resolver->companyHolidayForDate($date);

        $overrides = EmployeeShiftOverride::query()
            ->with(['employee', 'shift'])
            ->whereDate('date', $date)
            ->get();

        $leaves = Attendance::query()
            ->with('employee')
            ->whereDate('date', $date)
            ->where('status', Attendance::STATUS_LEAVE)
            ->get();

        return [
            'mode' => 'overview',
            'date' => $key,
            'date_label' => $date->translatedFormat('l, d F Y'),
            'company_holiday' => $holiday ? [
                'name' => $holiday->name,
                'notes' => $holiday->notes,
            ] : null,
            'libur' => $overrides->whereNull('shift_id')->map(fn ($o) => [
                'employee_id' => $o->employee_id,
                'employee_code' => $o->employee->employee_code,
                'employee_name' => $o->employee->name,
                'notes' => $o->notes,
                'override_id' => $o->id,
            ])->values(),
            'ganti_shift' => $overrides->whereNotNull('shift_id')->map(fn ($o) => [
                'employee_id' => $o->employee_id,
                'employee_code' => $o->employee->employee_code,
                'employee_name' => $o->employee->name,
                'shift' => $o->shift->code.' - '.$o->shift->name,
                'notes' => $o->notes,
                'override_id' => $o->id,
            ])->values(),
            'cuti' => $leaves->map(fn ($a) => [
                'employee_id' => $a->employee_id,
                'employee_code' => $a->employee->employee_code,
                'employee_name' => $a->employee->name,
                'notes' => $a->notes,
            ])->values(),
        ];
    }

    protected function employeeDayDetail(int $employeeId, Carbon $date): array
    {
        $employee = Employee::query()->with('shift')->findOrFail($employeeId);
        $key = $date->format('Y-m-d');

        $override = EmployeeShiftOverride::query()
            ->with('shift')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->first();

        $attendance = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->first();

        $shift = $this->resolver->shiftForDate($employee, $date);
        $holiday = $this->resolver->companyHolidayForDate($date);
        $isDayOff = $this->resolver->isDayOff($employee, $date);

        $statusLabel = 'Masuk';
        if ($attendance?->status === Attendance::STATUS_LEAVE) {
            $statusLabel = 'Cuti';
        } elseif ($isDayOff) {
            $statusLabel = $holiday && ! $override ? 'Libur Perusahaan' : 'Libur';
        }

        return [
            'mode' => 'employee',
            'date' => $key,
            'date_label' => $date->translatedFormat('l, d F Y'),
            'employee' => [
                'id' => $employee->id,
                'code' => $employee->employee_code,
                'name' => $employee->name,
            ],
            'status_label' => $statusLabel,
            'schedule_source' => $this->resolver->scheduleSource($employee, $date),
            'shift_label' => $this->resolver->shiftLabelForDate($employee, $date),
            'shift_time' => $shift
                ? substr($shift->start_time, 0, 5).' – '.substr($shift->end_time, 0, 5)
                : null,
            'company_holiday' => $holiday && ! $override ? [
                'name' => $holiday->name,
                'notes' => $holiday->notes,
            ] : null,
            'override' => $override ? [
                'id' => $override->id,
                'is_day_off' => $override->shift_id === null,
                'shift' => $override->shift?->code,
                'notes' => $override->notes,
            ] : null,
            'attendance' => $attendance ? [
                'status' => $attendance->status,
                'status_label' => attendance_status_label($attendance->status),
                'check_in' => $attendance->check_in_at?->format('H:i'),
                'check_out' => $attendance->check_out_at?->format('H:i'),
                'id' => $attendance->id,
            ] : null,
        ];
    }

    protected function legend(): array
    {
        return [
            ['class' => 'shift-cal-day--company', 'label' => 'Libur Perusahaan'],
            ['class' => 'shift-cal-day--off', 'label' => 'Libur'],
            ['class' => 'shift-cal-day--leave', 'label' => 'Cuti'],
            ['class' => 'shift-cal-day--work', 'label' => 'Masuk'],
            ['class' => 'shift-cal-day--override', 'label' => 'Ganti Shift'],
            ['class' => 'shift-cal-day--hadir', 'label' => 'Hadir'],
            ['class' => 'shift-cal-day--alpha', 'label' => 'Alpha'],
        ];
    }
}
