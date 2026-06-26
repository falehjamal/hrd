<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeDeductionDataTable;
use App\Http\Requests\StoreEmployeeDeductionRequest;
use App\Http\Requests\UpdateEmployeeDeductionRequest;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeDeductionController extends Controller
{
    public function indexAll(): View
    {
        return view('deductions.index');
    }

    public function dataAll(): JsonResponse
    {
        return (new EmployeeDeductionDataTable)->json();
    }

    public function dataForEmployee(Employee $employee): JsonResponse
    {
        return (new EmployeeDeductionDataTable($employee->id))->json();
    }

    public function create(Employee $employee): RedirectResponse
    {
        return redirect()
            ->route('employees.show', $employee)
            ->with('open_deduction_modal', 'create');
    }

    public function store(StoreEmployeeDeductionRequest $request, Employee $employee): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($data['is_active']) {
            EmployeeDeduction::deactivateOthersForEmployeeAndType(
                $employee->id,
                (int) $data['deduction_type_id']
            );
        }

        $employee->deductions()->create($data);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Pemotongan berhasil ditambahkan.');
    }

    public function show(EmployeeDeduction $deduction): JsonResponse
    {
        return response()->json([
            'deduction' => [
                'id' => $deduction->id,
                'deduction_type_id' => $deduction->deduction_type_id,
                'amount' => $deduction->amount,
                'effective_date' => $deduction->effective_date->format('Y-m-d'),
                'notes' => $deduction->notes,
                'is_active' => $deduction->is_active ? 1 : 0,
            ],
        ]);
    }

    public function edit(EmployeeDeduction $deduction): RedirectResponse
    {
        return redirect()
            ->route('employees.show', $deduction->employee_id)
            ->with('open_deduction_modal', $deduction->id);
    }

    public function update(UpdateEmployeeDeductionRequest $request, EmployeeDeduction $deduction): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_active']) {
            EmployeeDeduction::deactivateOthersForEmployeeAndType(
                $deduction->employee_id,
                (int) $data['deduction_type_id'],
                $deduction->id
            );
        }

        $deduction->update($data);

        return redirect()
            ->route('employees.show', $deduction->employee_id)
            ->with('success', 'Pemotongan berhasil diperbarui.');
    }

    public function destroy(EmployeeDeduction $deduction): RedirectResponse
    {
        $employeeId = $deduction->employee_id;
        $deduction->delete();

        return redirect()
            ->route('employees.show', $employeeId)
            ->with('success', 'Pemotongan berhasil dihapus.');
    }
}
