<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeSalaryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
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

    Route::resource('shifts', ShiftController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('employees.salaries', EmployeeSalaryController::class)->except(['show', 'index'])->shallow();
    Route::get('salaries', [EmployeeSalaryController::class, 'indexAll'])->name('salaries.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
