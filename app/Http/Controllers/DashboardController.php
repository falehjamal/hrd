<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\Shift;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalEmployees = Employee::query()->count();
        $activeEmployees = Employee::query()->active()->count();
        $presentToday = Attendance::query()->presentToday()->count();
        $lateToday = Attendance::query()->today()->where('status', Attendance::STATUS_LATE)->count();
        $newEmployeesThisMonth = Employee::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $attendanceRate = $activeEmployees > 0
            ? round(($presentToday / $activeEmployees) * 100)
            : 0;

        return view('dashboard.index', [
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'totalShifts' => Shift::query()->active()->count(),
            'activeSalaries' => EmployeeSalary::query()->active()->count(),
            'presentToday' => $presentToday,
            'lateToday' => $lateToday,
            'newEmployeesThisMonth' => $newEmployeesThisMonth,
            'attendanceRate' => $attendanceRate,
            'pendingLeaveRequests' => LeaveRequest::query()->pending()->count(),
            'pendingOvertimeRequests' => OvertimeRequest::query()->where('status', OvertimeRequest::STATUS_PENDING)->count(),
            'todayAttendances' => Attendance::query()
                ->with(['employee.organizationalUnit'])
                ->today()
                ->orderByDesc('check_in_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
