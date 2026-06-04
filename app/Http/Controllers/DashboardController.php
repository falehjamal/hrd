<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Shift;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard.index', [
            'totalEmployees' => Employee::query()->count(),
            'activeEmployees' => Employee::query()->active()->count(),
            'totalShifts' => Shift::query()->active()->count(),
            'activeSalaries' => EmployeeSalary::query()->active()->count(),
            'presentToday' => Attendance::query()->presentToday()->count(),
        ]);
    }
}
