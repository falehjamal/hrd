<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeSalaryRequest;
use App\Http\Requests\UpdateEmployeeSalaryRequest;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeSalaryController extends Controller
{
    public function indexAll(Request $request): View
    {
        $salaries = EmployeeSalary::query()
            ->with('employee')
            ->when($request->boolean('active_only', true), fn ($q) => $q->where('is_active', true))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('effective_date')
            ->paginate(15)
            ->withQueryString();

        return view('salaries.index', compact('salaries'));
    }

    public function index(Employee $employee): View
    {
        $salaries = $employee->salaries()->orderByDesc('effective_date')->paginate(15);

        return view('employees.salaries.index', compact('employee', 'salaries'));
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
