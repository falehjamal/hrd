<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeDataTable;
use App\Http\Concerns\HandlesCrudModal;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Branch;
use App\Models\DeductionType;
use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\Position;
use App\Models\Shift;
use App\Services\EmployeeAccountService;
use App\Services\EmployeeLeaveBalanceService;
use App\Services\EmployeePhotoService;
use App\Services\EmployeeWeeklyShiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    use HandlesCrudModal;

    public function __construct(
        private readonly EmployeePhotoService $employeePhotoService,
    ) {}

    protected function crudModalIndexRoute(): string
    {
        return 'employees.index';
    }

    protected function crudModalResourceKey(): string
    {
        return 'employee';
    }

    private const EMPLOYEE_FIELDS = [
        'employee_code',
        'name',
        'email',
        'phone',
        'national_id',
        'gender',
        'birth_date',
        'address',
        'position_id',
        'organizational_unit_id',
        'branch_id',
        'manager_id',
        'shift_id',
        'join_date',
        'status',
    ];

    public function index(): View
    {
        return view('employees.index', [
            'branches' => Branch::query()->active()->orderBy('name')->get(),
            'employee' => new Employee,
            ...$this->formOptions(),
        ]);
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

    public function create(): RedirectResponse
    {
        return $this->crudModalCreateRedirect();
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $sendNotification = $validated['send_notification'] ?? true;

        $role = ($validated['has_hr_access'] ?? false) ? 'hr' : 'employee';

        $result = DB::transaction(function () use ($validated, $sendNotification, $role, $request) {
            $employee = Employee::query()->create(Arr::only($validated, self::EMPLOYEE_FIELDS));

            if ($request->hasFile('photo')) {
                $employee->update([
                    'photo_path' => $this->employeePhotoService->storePhoto($employee, $request->file('photo')),
                ]);
            }

            return app(EmployeeAccountService::class)->createAutoForEmployee(
                $employee,
                $validated['username'] ?? null,
                $validated['password'] ?? null,
                $sendNotification,
                $role,
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

    public function show(Request $request, Employee $employee): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $employee->load('user');

            return response()->json([
                'employee' => $this->employeeFormJson($employee),
            ]);
        }

        $employee->load([
            'shift', 'user', 'position', 'organizationalUnit', 'branch', 'manager', 'weeklyShifts.shift',
            'activeDeductions.deductionType', 'activeLoans',
        ]);
        $weeklyShifts = app(EmployeeWeeklyShiftService::class)->shiftsIndexedByDay($employee);
        $leaveYear = (int) now()->year;
        $leaveBalances = app(EmployeeLeaveBalanceService::class)->ensureBalancesForYear($employee, $leaveYear);

        foreach ($leaveBalances as $balance) {
            app(EmployeeLeaveBalanceService::class)->syncUsedDays($balance);
        }

        $leaveBalances = app(EmployeeLeaveBalanceService::class)->ensureBalancesForYear($employee, $leaveYear);

        return view('employees.show', [
            'employee' => $employee,
            'weeklyShifts' => $weeklyShifts,
            'leaveBalances' => $leaveBalances,
            'leaveYear' => $leaveYear,
            'shifts' => Shift::query()->active()->orderBy('name')->get(),
            'deductionTypes' => DeductionType::query()->active()->orderBy('code')->get(),
            'deduction' => null,
            'salary' => null,
            'openWeeklyShiftModal' => session('open_weekly_shift_modal'),
            'openLeaveBalanceModal' => session('open_leave_balance_modal'),
            ...$this->formOptions($employee),
        ]);
    }

    public function edit(Employee $employee): RedirectResponse
    {
        return $this->crudModalEditRedirect($employee);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validated();
        $sendNotification = $validated['send_notification'] ?? true;

        $role = ($validated['has_hr_access'] ?? false) ? 'hr' : 'employee';

        DB::transaction(function () use ($employee, $validated, $sendNotification, $role, $request) {
            $employee->update(Arr::only($validated, self::EMPLOYEE_FIELDS));

            if ($request->hasFile('photo')) {
                $employee->update([
                    'photo_path' => $this->employeePhotoService->replacePhoto($employee, $request->file('photo')),
                ]);
            }

            app(EmployeeAccountService::class)->createOrSyncForEmployee(
                $employee->refresh(),
                $validated['username'] ?? null,
                $validated['password'] ?? null,
                $sendNotification,
                $role,
            );
        });

        $message = 'Karyawan berhasil diperbarui.';

        if ($sendNotification) {
            $message .= ' Notifikasi telah dikirim.';
        }

        if ($request->input('_return_to') === 'show') {
            return redirect()->route('employees.show', $employee)->with('success', $message);
        }

        return redirect()->route('employees.index')->with('success', $message);
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        DB::transaction(function () use ($employee) {
            $this->employeePhotoService->deletePhoto($employee->photo_path);
            $user = $employee->user;
            $employee->delete();
            $user?->delete();
        });

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }

    public function photo(Employee $employee): Response
    {
        if (! $this->employeePhotoService->canViewPhoto($employee, request()->user())) {
            abort(403);
        }

        $path = $employee->photo_path;

        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $disk = Storage::disk('local');

        return response($disk->get($path), 200, [
            'Content-Type' => $disk->mimeType($path),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function employeeFormJson(Employee $employee): array
    {
        return [
            'id' => $employee->id,
            'employee_code' => $employee->employee_code,
            'name' => $employee->name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'national_id' => $employee->national_id,
            'gender' => $employee->gender,
            'birth_date' => $employee->birth_date?->format('Y-m-d'),
            'address' => $employee->address,
            'position_id' => $employee->position_id,
            'organizational_unit_id' => $employee->organizational_unit_id,
            'branch_id' => $employee->branch_id,
            'manager_id' => $employee->manager_id,
            'shift_id' => $employee->shift_id,
            'join_date' => $employee->join_date?->format('Y-m-d'),
            'status' => $employee->status,
            'username' => $employee->user?->username,
            'has_hr_access' => $employee->user?->isHrUser() ? 1 : 0,
            'send_notification' => 0,
            'photo_url' => $employee->photo_url,
        ];
    }

    /**
     * @return array{shifts: Collection, positions: Collection, units: Collection, branches: Collection, managers: Collection}
     */
    private function formOptions(?Employee $exclude = null): array
    {
        return [
            'shifts' => Shift::query()->active()->orderBy('name')->get(),
            'positions' => Position::query()->active()->orderBy('level')->orderBy('name')->get(),
            'units' => OrganizationalUnit::query()->active()->orderBy('name')->get(),
            'branches' => Branch::query()->active()->orderBy('name')->get(),
            'managers' => Employee::query()
                ->active()
                ->when($exclude, fn ($q) => $q->where('id', '!=', $exclude->id))
                ->orderBy('name')
                ->get(),
        ];
    }
}
