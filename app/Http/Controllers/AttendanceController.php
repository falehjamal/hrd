<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceDataTable;
use App\Http\Concerns\HandlesCrudModal;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use App\Services\EmployeeShiftResolverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    use HandlesCrudModal;

    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    protected function crudModalIndexRoute(): string
    {
        return 'attendances.index';
    }

    protected function crudModalResourceKey(): string
    {
        return 'attendance';
    }

    public function index(): View
    {
        $employees = Employee::query()->active()->orderBy('name')->get(['id', 'employee_code', 'name']);

        return view('attendances.index', [
            'employees' => $employees,
            'statuses' => Attendance::statusLabels(),
            'attendance' => null,
        ]);
    }

    public function data(AttendanceDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function resolvedShift(Request $request, EmployeeShiftResolverService $resolver): JsonResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
        ]);

        $employee = Employee::query()->findOrFail($data['employee_id']);

        return response()->json([
            'label' => $resolver->shiftLabelForDate($employee, $data['date']),
            'is_day_off' => $resolver->isDayOff($employee, $data['date']),
        ]);
    }

    public function create(): RedirectResponse
    {
        return $this->crudModalCreateRedirect();
    }

    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        try {
            $this->attendanceService->storeManual(
                $request->validated(),
                $request->file('check_in_photo'),
                $request->file('check_out_photo'),
                (int) $request->user()->id
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('attendances.index')
                ->withInput()
                ->with('error', $e->getMessage())
                ->with('open_crud_modal', 'create');
        }

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil ditambahkan.');
    }

    public function show(Attendance $attendance): JsonResponse
    {
        $attendance->load('employee');

        return response()->json([
            'attendance' => [
                'id' => $attendance->id,
                'employee_id' => $attendance->employee_id,
                'date' => $attendance->date->format('Y-m-d'),
                'check_in_time' => $attendance->check_in_at?->format('H:i'),
                'check_out_time' => $attendance->check_out_at?->format('H:i'),
                'status' => $attendance->status,
                'notes' => $attendance->notes,
                'activity_notes' => $attendance->activity_notes,
            ],
        ]);
    }

    public function edit(Attendance $attendance): RedirectResponse
    {
        return $this->crudModalEditRedirect($attendance);
    }

    public function update(UpdateAttendanceRequest $request, Attendance $attendance): RedirectResponse
    {
        try {
            $this->attendanceService->updateManual(
                $attendance,
                $request->validated(),
                $request->file('check_in_photo'),
                $request->file('check_out_photo'),
                (int) $request->user()->id
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('attendances.index')
                ->withInput()
                ->with('error', $e->getMessage())
                ->with('open_crud_modal', $attendance->id);
        }

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $this->attendanceService->deleteAttendance($attendance);

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil dihapus.');
    }

    public function photo(Attendance $attendance, string $type): Response
    {
        if (! $this->attendanceService->canViewPhoto($attendance, request()->user())) {
            abort(403);
        }

        $path = $type === 'check-out'
            ? $attendance->check_out_photo_path
            : $attendance->check_in_photo_path;

        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response(Storage::disk('local')->get($path), 200, [
            'Content-Type' => Storage::disk('local')->mimeType($path),
        ]);
    }
}
