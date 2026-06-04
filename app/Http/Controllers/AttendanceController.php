<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceDataTable;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function index(): View
    {
        $employees = Employee::query()->active()->orderBy('name')->get(['id', 'employee_code', 'name']);

        return view('attendances.index', compact('employees'));
    }

    public function data(AttendanceDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function create(): View
    {
        $employees = Employee::query()->active()->orderBy('name')->get();
        $statuses = Attendance::statusLabels();

        return view('attendances.create', compact('employees', 'statuses'));
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
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil ditambahkan.');
    }

    public function edit(Attendance $attendance): View
    {
        $attendance->load('employee');
        $employees = Employee::query()->active()->orderBy('name')->get();
        $statuses = Attendance::statusLabels();

        return view('attendances.edit', compact('attendance', 'employees', 'statuses'));
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
            return back()->withInput()->with('error', $e->getMessage());
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
