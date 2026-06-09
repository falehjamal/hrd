<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeDataTable;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Shift;
use App\Services\EmployeeAccountService;
use App\Services\EmployeeWeeklyShiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    private const EMPLOYEE_FIELDS = [
        'employee_code',
        'name',
        'email',
        'phone',
        'department',
        'position',
        'shift_id',
        'join_date',
        'status',
    ];

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
        $validated = $request->validated();

        $sendNotification = $validated['send_notification'] ?? true;

        $result = DB::transaction(function () use ($validated, $sendNotification) {
            $employee = Employee::query()->create(Arr::only($validated, self::EMPLOYEE_FIELDS));

            return app(EmployeeAccountService::class)->createAutoForEmployee(
                $employee,
                $validated['username'] ?? null,
                $validated['password'] ?? null,
                $sendNotification,
            );
        });

        $message = 'Karyawan berhasil ditambahkan. Akun login: '.$result['user']->username.' | Password: '.$result['password'];

        if ($sendNotification) {
            $message .= ' Notifikasi telah dikirim.';
        }

        return redirect()
            ->route('employees.index')
            ->with('success', $message);
    }

    public function show(Employee $employee): View
    {
        $employee->load(['shift', 'user', 'weeklyShifts.shift']);
        $weeklyShifts = app(EmployeeWeeklyShiftService::class)->shiftsIndexedByDay($employee);

        return view('employees.show', compact('employee', 'weeklyShifts'));
    }

    public function edit(Employee $employee): View
    {
        $employee->load('user');
        $shifts = Shift::query()->active()->orderBy('name')->get();

        return view('employees.edit', compact('employee', 'shifts'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validated();
        $sendNotification = $validated['send_notification'] ?? true;

        DB::transaction(function () use ($employee, $validated, $sendNotification) {
            $employee->update(Arr::only($validated, self::EMPLOYEE_FIELDS));

            app(EmployeeAccountService::class)->createOrSyncForEmployee(
                $employee->refresh(),
                $validated['username'] ?? null,
                $validated['password'] ?? null,
                $sendNotification,
            );
        });

        $message = 'Karyawan berhasil diperbarui.';

        if ($sendNotification) {
            $message .= ' Notifikasi telah dikirim.';
        }

        return redirect()->route('employees.index')->with('success', $message);
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        DB::transaction(function () use ($employee) {
            $user = $employee->user;
            $employee->delete();
            $user?->delete();
        });

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
