<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmployeeWeeklyShiftRequest;
use App\Models\Employee;
use App\Services\EmployeeWeeklyShiftService;
use Illuminate\Http\RedirectResponse;

class EmployeeWeeklyShiftController extends Controller
{
    public function edit(Employee $employee): RedirectResponse
    {
        return redirect()
            ->route('employees.show', $employee)
            ->with('open_weekly_shift_modal', '1');
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
