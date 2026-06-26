<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceCheckInRequest;
use App\Models\Attendance;
use App\Services\AttendanceGeofenceService;
use App\Services\AttendanceService;
use App\Services\EmployeeShiftResolverService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceCheckInController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
        protected AttendanceGeofenceService $geofence,
        protected EmployeeShiftResolverService $shiftResolver
    ) {}

    public function create(): View
    {
        $employee = auth()->user()->employee;
        $employee->load('branch');
        $todayAttendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->first();

        $location = $this->geofence->locationForEmployee($employee);
        $todayShiftLabel = $this->shiftResolver->shiftLabelForDate($employee, today());
        $isDayOff = $this->shiftResolver->isDayOff($employee, today());

        return view('attendances.check-in', compact('employee', 'todayAttendance', 'location', 'todayShiftLabel', 'isDayOff'));
    }

    public function store(StoreAttendanceCheckInRequest $request): RedirectResponse
    {
        $employee = $request->user()->employee;

        try {
            if ($request->input('action') === 'check_out') {
                $this->attendanceService->checkOutGps(
                    $employee,
                    (float) $request->latitude,
                    (float) $request->longitude,
                    $request->file('photo'),
                    (int) $request->user()->id,
                    $request->input('activity_notes')
                );
                $message = 'Absen pulang berhasil dicatat.';
            } else {
                $this->attendanceService->checkInGps(
                    $employee,
                    (float) $request->latitude,
                    (float) $request->longitude,
                    $request->file('photo'),
                    (int) $request->user()->id
                );
                $message = 'Absen masuk berhasil dicatat.';
            }
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('attendances.check-in')->with('success', $message);
    }
}
