<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeSalaryDataTable;
use App\Http\Requests\StoreEmployeeSalaryRequest;
use App\Http\Requests\UpdateEmployeeSalaryRequest;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeSalaryController extends Controller
{
    public function indexAll(): View
    {
        return view('salaries.index');
    }

    public function dataAll(): JsonResponse
    {
        return (new EmployeeSalaryDataTable)->json();
    }

    public function dataForEmployee(Employee $employee): JsonResponse
    {
        return (new EmployeeSalaryDataTable($employee->id))->json();
    }

    public function create(Employee $employee): View
    {
        return view('employees.salaries.create', compact('employee'));
    }

    public function store(StoreEmployeeSalaryRequest $request, Employee $employee): RedirectResponse
    {
        $data = $request->validated();
        $data['fixed_allowance'] = $data['fixed_allowance'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        if ($data['is_active']) {
            EmployeeSalary::deactivateOthersForEmployee($employee->id);
        }

        $employee->salaries()->create($data);

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Data gaji berhasil ditambahkan.');
    }

    public function edit(EmployeeSalary $salary): View
    {
        $salary->load('employee');

        return view('employees.salaries.edit', [
            'employee' => $salary->employee,
            'salary' => $salary,
        ]);
    }

    public function update(UpdateEmployeeSalaryRequest $request, EmployeeSalary $salary): RedirectResponse
    {
        $data = $request->validated();
        $data['fixed_allowance'] = $data['fixed_allowance'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_active']) {
            EmployeeSalary::deactivateOthersForEmployee($salary->employee_id, $salary->id);
        }

        $salary->update($data);

        return redirect()
            ->route('employees.show', $salary->employee_id)
            ->with('success', 'Data gaji berhasil diperbarui.');
    }

    public function destroy(EmployeeSalary $salary): RedirectResponse
    {
        $employeeId = $salary->employee_id;
        $salary->delete();

        return redirect()
            ->route('employees.show', $employeeId)
            ->with('success', 'Data gaji berhasil dihapus.');
    }
}
