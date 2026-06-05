<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeWeeklyShift;

class EmployeeWeeklyShiftService
{
    public function syncForEmployee(Employee $employee, array $shiftsByDay): void
    {
        foreach (range(1, 7) as $day) {
            $shiftId = $shiftsByDay[$day] ?? $shiftsByDay[(string) $day] ?? null;
            $shiftId = $shiftId === '' || $shiftId === 'default' ? null : $shiftId;

            EmployeeWeeklyShift::query()->updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'day_of_week' => $day,
                ],
                [
                    'shift_id' => $shiftId,
                ]
            );
        }
    }

    public function shiftsIndexedByDay(Employee $employee): array
    {
        $rows = $employee->weeklyShifts()->get()->keyBy('day_of_week');
        $result = [];

        foreach (range(1, 7) as $day) {
            $result[$day] = $rows->get($day)?->shift_id;
        }

        return $result;
    }
}
