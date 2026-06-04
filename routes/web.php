<?php

use App\Http\Controllers\AttendanceCheckInController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeSalaryController;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\WorkLocationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('employees/data', [EmployeeController::class, 'data'])->name('employees.data');
    Route::get('shifts/data', [ShiftController::class, 'data'])->name('shifts.data');
    Route::get('salaries/data', [EmployeeSalaryController::class, 'dataAll'])->name('salaries.data');
    Route::get('employees/{employee}/salaries/data', [EmployeeSalaryController::class, 'dataForEmployee'])->name('employees.salaries.data');

    Route::get('work-locations/data', [WorkLocationController::class, 'data'])->name('work-locations.data');
    Route::get('attendances/data', [AttendanceController::class, 'data'])->name('attendances.data');
    Route::get('attendances/{attendance}/photo/{type}', [AttendanceController::class, 'photo'])
        ->name('attendances.photo')
        ->where('type', 'check-in|check-out');
    Route::get('overtime-requests/data', [OvertimeRequestController::class, 'data'])->name('overtime-requests.data');

    Route::middleware('employee.linked')->group(function () {
        Route::get('absen', [AttendanceCheckInController::class, 'create'])->name('attendances.check-in');
        Route::post('absen', [AttendanceCheckInController::class, 'store'])->name('attendances.check-in.store');
    });

    Route::resource('work-locations', WorkLocationController::class)->except(['show']);
    Route::resource('attendances', AttendanceController::class)->except(['show']);
    Route::resource('overtime-requests', OvertimeRequestController::class)->except(['show', 'edit', 'update']);
    Route::patch('overtime-requests/{overtime_request}/approve', [OvertimeRequestController::class, 'approve'])->name('overtime-requests.approve');
    Route::patch('overtime-requests/{overtime_request}/reject', [OvertimeRequestController::class, 'reject'])->name('overtime-requests.reject');

    Route::post('employees/{employee}/account', [EmployeeController::class, 'storeAccount'])->name('employees.account.store');

    Route::resource('shifts', ShiftController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('employees.salaries', EmployeeSalaryController::class)->except(['show', 'index'])->shallow();
    Route::get('salaries', [EmployeeSalaryController::class, 'indexAll'])->name('salaries.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
