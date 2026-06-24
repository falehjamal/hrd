<?php

use App\Http\Controllers\AttendanceCheckInController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyHolidayController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeductionTypeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeDeductionController;
use App\Http\Controllers\EmployeeLeaveBalanceController;
use App\Http\Controllers\EmployeeLoanController;
use App\Http\Controllers\EmployeeLoanInstallmentController;
use App\Http\Controllers\EmployeeSalaryController;
use App\Http\Controllers\EmployeeWeeklyShiftController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\OrganizationalUnitController;
use App\Http\Controllers\OrganizationStructureController;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftOverrideController;
use App\Http\Controllers\WhatsAppSessionController;
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

    Route::middleware('hr.user')->group(function () {
        Route::get('deduction-types/data', [DeductionTypeController::class, 'data'])->name('deduction-types.data');
        Route::resource('deduction-types', DeductionTypeController::class)->except(['show']);
        Route::get('deductions/data', [EmployeeDeductionController::class, 'dataAll'])->name('deductions.data');
        Route::get('employees/{employee}/deductions/data', [EmployeeDeductionController::class, 'dataForEmployee'])->name('employees.deductions.data');
        Route::resource('employees.deductions', EmployeeDeductionController::class)->except(['show', 'index'])->shallow();
        Route::get('deductions', [EmployeeDeductionController::class, 'indexAll'])->name('deductions.index');
        Route::get('employee-loans/data', [EmployeeLoanController::class, 'data'])->name('employee-loans.data');
        Route::get('employee-loans/preview', [EmployeeLoanController::class, 'preview'])->name('employee-loans.preview');
        Route::get('employees/{employee}/employee-loans/data', [EmployeeLoanController::class, 'dataForEmployee'])->name('employees.employee-loans.data');
        Route::resource('employee-loans', EmployeeLoanController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::patch('employee-loans/{employee_loan}/cancel', [EmployeeLoanController::class, 'cancel'])->name('employee-loans.cancel');
        Route::patch('employee-loan-installments/{employee_loan_installment}/pay', [EmployeeLoanInstallmentController::class, 'pay'])->name('employee-loan-installments.pay');
        Route::get('leave-types/data', [LeaveTypeController::class, 'data'])->name('leave-types.data');
        Route::resource('leave-types', LeaveTypeController::class)->except(['show']);
        Route::get('employees/{employee}/leave-balances/data', [EmployeeLeaveBalanceController::class, 'data'])->name('employees.leave-balances.data');
        Route::get('employees/{employee}/leave-balances/edit', [EmployeeLeaveBalanceController::class, 'edit'])->name('employees.leave-balances.edit');
        Route::put('employees/{employee}/leave-balances', [EmployeeLeaveBalanceController::class, 'update'])->name('employees.leave-balances.update');
        Route::get('employees/{employee}/leave-requests/data', [LeaveRequestController::class, 'dataForEmployee'])->name('employees.leave-requests.data');
        Route::get('employees/data', [EmployeeController::class, 'data'])->name('employees.data');
        Route::get('employees/search', [EmployeeController::class, 'search'])->name('employees.search');
        Route::get('positions/data', [PositionController::class, 'data'])->name('positions.data');
        Route::get('organizational-units/data', [OrganizationalUnitController::class, 'data'])->name('organizational-units.data');
        Route::get('branches/data', [BranchController::class, 'data'])->name('branches.data');
        Route::get('organization-structure', [OrganizationStructureController::class, 'index'])->name('organization-structure.index');
        Route::get('salaries/data', [EmployeeSalaryController::class, 'dataAll'])->name('salaries.data');
        Route::get('employees/{employee}/salaries/data', [EmployeeSalaryController::class, 'dataForEmployee'])->name('employees.salaries.data');
        Route::get('employees/{employee}/weekly-shifts/edit', [EmployeeWeeklyShiftController::class, 'edit'])->name('employees.weekly-shifts.edit');
        Route::put('employees/{employee}/weekly-shifts', [EmployeeWeeklyShiftController::class, 'update'])->name('employees.weekly-shifts.update');
        Route::resource('positions', PositionController::class)->except(['show']);
        Route::resource('organizational-units', OrganizationalUnitController::class)->except(['show']);
        Route::resource('branches', BranchController::class)->except(['show']);
        Route::resource('employees', EmployeeController::class);
        Route::resource('employees.salaries', EmployeeSalaryController::class)->except(['show', 'index'])->shallow();
        Route::get('salaries', [EmployeeSalaryController::class, 'indexAll'])->name('salaries.index');
        Route::get('payroll-periods/data', [PayrollPeriodController::class, 'data'])->name('payroll-periods.data');
        Route::get('payroll-periods/create', [PayrollPeriodController::class, 'create'])->name('payroll-periods.create');
        Route::post('payroll-periods', [PayrollPeriodController::class, 'store'])->name('payroll-periods.store');
        Route::get('payroll-periods/{payroll_period}', [PayrollPeriodController::class, 'show'])->name('payroll-periods.show');
        Route::get('payroll-periods/{payroll_period}/entries/data', [PayrollPeriodController::class, 'entriesData'])->name('payroll-periods.entries.data');
        Route::get('payroll-periods/{payroll_period}/entries/{entry}', [PayrollPeriodController::class, 'showEntry'])->name('payroll-periods.entries.show');
        Route::post('payroll-periods/{payroll_period}/regenerate', [PayrollPeriodController::class, 'regenerate'])->name('payroll-periods.regenerate');
        Route::patch('payroll-periods/{payroll_period}/finalize', [PayrollPeriodController::class, 'finalize'])->name('payroll-periods.finalize');
        Route::delete('payroll-periods/{payroll_period}', [PayrollPeriodController::class, 'destroy'])->name('payroll-periods.destroy');
        Route::get('payroll-periods', [PayrollPeriodController::class, 'index'])->name('payroll-periods.index');
    });

    Route::get('shifts/data', [ShiftController::class, 'data'])->name('shifts.data');

    Route::get('work-locations/data', [WorkLocationController::class, 'data'])->name('work-locations.data');
    Route::get('attendances/data', [AttendanceController::class, 'data'])->name('attendances.data');
    Route::get('attendances/resolved-shift', [AttendanceController::class, 'resolvedShift'])->name('attendances.resolved-shift');
    Route::get('shift-overrides/calendar', [ShiftOverrideController::class, 'calendar'])->name('shift-overrides.calendar');
    Route::get('shift-overrides/day-detail', [ShiftOverrideController::class, 'dayDetail'])->name('shift-overrides.day-detail');
    Route::get('shift-overrides/data', [ShiftOverrideController::class, 'data'])->name('shift-overrides.data');
    Route::get('company-holidays/data', [CompanyHolidayController::class, 'index'])->name('company-holidays.data');
    Route::post('company-holidays', [CompanyHolidayController::class, 'store'])->name('company-holidays.store');
    Route::put('company-holidays/{company_holiday}', [CompanyHolidayController::class, 'update'])->name('company-holidays.update');
    Route::delete('company-holidays/{company_holiday}', [CompanyHolidayController::class, 'destroy'])->name('company-holidays.destroy');
    Route::get('employees/{employee}/photo', [EmployeeController::class, 'photo'])->name('employees.photo');
    Route::get('attendances/{attendance}/photo/{type}', [AttendanceController::class, 'photo'])
        ->name('attendances.photo')
        ->where('type', 'check-in|check-out');
    Route::get('leave-requests/data', [LeaveRequestController::class, 'data'])->name('leave-requests.data');
    Route::get('leave-requests/calculate-days', [LeaveRequestController::class, 'calculateDays'])->name('leave-requests.calculate-days');
    Route::get('overtime-requests/data', [OvertimeRequestController::class, 'data'])->name('overtime-requests.data');

    Route::middleware('employee.linked')->group(function () {
        Route::get('absen', [AttendanceCheckInController::class, 'create'])->name('attendances.check-in');
        Route::post('absen', [AttendanceCheckInController::class, 'store'])->name('attendances.check-in.store');
        Route::get('payslips/data', [PayslipController::class, 'data'])->name('payslips.data');
        Route::get('payslips/{entry}', [PayslipController::class, 'show'])->name('payslips.show');
        Route::get('payslips', [PayslipController::class, 'index'])->name('payslips.index');
    });

    Route::resource('work-locations', WorkLocationController::class)->except(['show']);
    Route::resource('attendances', AttendanceController::class)->except(['show']);
    Route::resource('overtime-requests', OvertimeRequestController::class)->except(['show', 'edit', 'update']);
    Route::resource('leave-requests', LeaveRequestController::class)->except(['show', 'edit', 'update']);
    Route::patch('leave-requests/{leave_request}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::patch('leave-requests/{leave_request}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::patch('overtime-requests/{overtime_request}/approve', [OvertimeRequestController::class, 'approve'])->name('overtime-requests.approve');
    Route::patch('overtime-requests/{overtime_request}/reject', [OvertimeRequestController::class, 'reject'])->name('overtime-requests.reject');

    Route::resource('shift-overrides', ShiftOverrideController::class)->except(['show']);

    Route::resource('shifts', ShiftController::class);

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/whatsapp/connect', [WhatsAppSessionController::class, 'connect'])->name('settings.wa.connect');
    Route::get('/settings/whatsapp/status', [WhatsAppSessionController::class, 'status'])->name('settings.wa.status');
    Route::delete('/settings/whatsapp/disconnect', [WhatsAppSessionController::class, 'disconnect'])->name('settings.wa.disconnect');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
