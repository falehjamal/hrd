<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeDataTable;
use App\Http\Requests\StoreEmployeeAccountRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Shift;
use App\Services\EmployeeAccountService;
use App\Services\EmployeeWeeklyShiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        $employees = Employee::query()
            ->active()
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('employee_code', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'employee_code', 'name']);

        return response()->json([
            'results' => $employees->map(fn (Employee $employee) => [
                'id' => $employee->id,
                'text' => $employee->employee_code.' — '.$employee->name,
            ]),
        ]);
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
        $employee->load(['shift', 'user', 'weeklyShifts.shift']);
        $weeklyShifts = app(EmployeeWeeklyShiftService::class)->shiftsIndexedByDay($employee);

        return view('employees.show', compact('employee', 'weeklyShifts'));
    }

    public function storeAccount(StoreEmployeeAccountRequest $request, Employee $employee): RedirectResponse
    {
        try {
            app(EmployeeAccountService::class)->createForEmployee($employee, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Akun login karyawan berhasil dibuat.');
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
