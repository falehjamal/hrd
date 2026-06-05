<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmployeeWeeklyShiftRequest;
use App\Models\Employee;
use App\Models\Shift;
use App\Services\EmployeeWeeklyShiftService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeWeeklyShiftController extends Controller
{
    public function edit(Employee $employee): View
    {
        $employee->load('shift');
        $shifts = Shift::query()->active()->orderBy('name')->get();
        $weeklyShifts = app(EmployeeWeeklyShiftService::class)->shiftsIndexedByDay($employee);

        return view('employees.weekly-shifts.edit', compact('employee', 'shifts', 'weeklyShifts'));
    }

    public function update(UpdateEmployeeWeeklyShiftRequest $request, Employee $employee): RedirectResponse
    {
        app(EmployeeWeeklyShiftService::class)->syncForEmployee(
            $employee,
            $request->validated('shifts', [])
        );

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Pola shift mingguan berhasil disimpan.');
    }
}
