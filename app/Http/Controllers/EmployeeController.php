<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeDataTable;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        return view('employees.index');
    }

    public function data(EmployeeDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function create(): View
    {
        $shifts = Shift::query()->active()->orderBy('name')->get();

        return view('employees.create', compact('shifts'));
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        Employee::query()->create($request->validated());

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee): View
    {
        $employee->load(['shift']);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $shifts = Shift::query()->active()->orderBy('name')->get();

        return view('employees.edit', compact('employee', 'shifts'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
