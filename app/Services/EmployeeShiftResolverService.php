<?php

namespace App\Services;

use App\Models\CompanyHoliday;
use App\Models\Employee;
use App\Models\EmployeeShiftOverride;
use App\Models\EmployeeWeeklyShift;
use App\Models\Shift;
use Carbon\Carbon;

class EmployeeShiftResolverService
{
    public function companyHolidayForDate(Carbon|string $date): ?CompanyHoliday
    {
        $date = Carbon::parse($date)->startOfDay();

        return CompanyHoliday::query()
            ->active()
            ->whereDate('date', $date)
            ->first();
    }

    public function isCompanyHoliday(Carbon|string $date): bool
    {
        return $this->companyHolidayForDate($date) !== null;
    }

    public function shiftForDate(Employee $employee, Carbon|string $date): ?Shift
    {
        $date = Carbon::parse($date)->startOfDay();
        $employee->loadMissing('shift');

        $override = $this->overrideForDate($employee->id, $date);

        if ($override) {
            return $override->shift_id ? $override->shift : null;
        }

        if ($this->isCompanyHoliday($date)) {
            return null;
        }

        $weekly = EmployeeWeeklyShift::query()
            ->where('employee_id', $employee->id)
            ->where('day_of_week', $date->isoWeekday())
            ->first();

        if ($weekly?->shift_id) {
            return $weekly->shift;
        }

        return $employee->shift;
    }

    public function scheduleSource(Employee $employee, Carbon|string $date): string
    {
        $date = Carbon::parse($date)->startOfDay();

        $override = $this->overrideForDate($employee->id, $date);

        if ($override) {
            return $override->shift_id ? 'override' : 'libur';
        }

        if ($this->isCompanyHoliday($date)) {
            return 'libur_perusahaan';
        }

        $weekly = EmployeeWeeklyShift::query()
            ->where('employee_id', $employee->id)
            ->where('day_of_week', $date->isoWeekday())
            ->whereNotNull('shift_id')
            ->exists();

        return $weekly ? 'weekly' : 'default';
    }

    public function shiftLabelForDate(Employee $employee, Carbon|string $date): string
    {
        $date = Carbon::parse($date)->startOfDay();

        $override = $this->overrideForDate($employee->id, $date);

        if ($override) {
            if ($override->shift_id === null) {
                return 'Libur';
            }

            $override->loadMissing('shift');

            return $override->shift->code.' - '.$override->shift->name;
        }

        $companyHoliday = $this->companyHolidayForDate($date);

        if ($companyHoliday) {
            return 'Libur Perusahaan: '.$companyHoliday->name;
        }

        $shift = $this->shiftForDate($employee, $date);

        if (! $shift) {
            return 'Tanpa shift';
        }

        $source = $this->scheduleSource($employee, $date);

        if ($source === 'weekly') {
            return $shift->code.' - '.$shift->name;
        }

        return $shift->code.' - '.$shift->name.' (default)';
    }

    public function isDayOff(Employee $employee, Carbon|string $date): bool
    {
        $date = Carbon::parse($date)->startOfDay();

        $override = $this->overrideForDate($employee->id, $date);

        if ($override) {
            return $override->shift_id === null;
        }

        return $this->isCompanyHoliday($date);
    }

    public function shiftStartForCheckIn(?Shift $shift, Carbon $checkInAt): ?Carbon
    {
        if (! $shift) {
            return null;
        }

        $date = $checkInAt->copy()->startOfDay();
        $start = Carbon::parse($date->format('Y-m-d').' '.$shift->start_time);
        $end = Carbon::parse($date->format('Y-m-d').' '.$shift->end_time);

        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();

            if ($checkInAt->lessThan($end) && $checkInAt->format('H:i:s') <= $shift->end_time) {
                $start->subDay();
            }
        }

        return $start;
    }

    protected function overrideForDate(int $employeeId, Carbon $date): ?EmployeeShiftOverride
    {
        return EmployeeShiftOverride::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->first();
    }
}
