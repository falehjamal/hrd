<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeLeaveBalanceDataTable;
use App\Http\Requests\UpdateEmployeeLeaveBalancesRequest;
use App\Models\Employee;
use App\Services\EmployeeLeaveBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeLeaveBalanceController extends Controller
{
    public function __construct(
        protected EmployeeLeaveBalanceService $balanceService
    ) {}

    public function edit(Request $request, Employee $employee): View
    {
        $year = (int) $request->input('year', now()->year);
        $balances = $this->balanceService->ensureBalancesForYear($employee, $year);

        foreach ($balances as $balance) {
            $this->balanceService->syncUsedDays($balance);
        }

        $balances = $this->balanceService->ensureBalancesForYear($employee, $year);

        return view('employees.leave-balances.edit', compact('employee', 'balances', 'year'));
    }

    public function update(UpdateEmployeeLeaveBalancesRequest $request, Employee $employee): RedirectResponse
    {
        $year = (int) $request->validated('year');

        $this->balanceService->updateQuotas($employee, $year, $request->validated('balances'));

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Kuota cuti berhasil diperbarui.');
    }

    public function data(Request $request, Employee $employee): JsonResponse
    {
        $year = (int) $request->input('year', now()->year);

        $this->balanceService->ensureBalancesForYear($employee, $year);

        return (new EmployeeLeaveBalanceDataTable($employee->id, $year))->json();
    }
}
