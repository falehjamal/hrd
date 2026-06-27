<?php

namespace App\Http\Controllers;

use App\DataTables\Reports\AttendanceSummaryDataTable;
use App\DataTables\Reports\LeaveSummaryDataTable;
use App\DataTables\Reports\PayrollSummaryDataTable;
use App\Models\Branch;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\OrganizationalUnit;
use App\Services\Reports\AttendanceReportService;
use App\Services\Reports\LeaveReportService;
use App\Services\Reports\PayrollReportService;
use App\Services\Reports\ReportScopeService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportScopeService $scope,
        protected AttendanceReportService $attendanceReport,
        protected LeaveReportService $leaveReport,
        protected PayrollReportService $payrollReport,
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        return view('reports.index', [
            'scopeLabel' => $this->scope->scopeLabel($user),
            'scopedCount' => $this->scope->scopedEmployeeCount($user),
        ]);
    }

    public function attendance(): View
    {
        $user = auth()->user();

        return view('reports.attendance', [
            'scopeLabel' => $this->scope->scopeLabel($user),
            'isHr' => $user->isHrUser(),
            'branches' => Branch::query()->active()->orderBy('name')->get(),
            'units' => OrganizationalUnit::query()->active()->orderBy('name')->get(),
            'dateFrom' => request('date_from', $this->attendanceReport->defaultDateFrom()),
            'dateTo' => request('date_to', $this->attendanceReport->defaultDateTo()),
            'summary' => $this->attendanceReport->summary($user),
        ]);
    }

    public function attendanceData(): JsonResponse
    {
        return (new AttendanceSummaryDataTable(auth()->user(), $this->scope))->json();
    }

    public function attendanceSummary(): JsonResponse
    {
        return response()->json($this->attendanceReport->summary(auth()->user()));
    }

    public function leave(): View
    {
        $user = auth()->user();

        return view('reports.leave', [
            'scopeLabel' => $this->scope->scopeLabel($user),
            'isHr' => $user->isHrUser(),
            'branches' => Branch::query()->active()->orderBy('name')->get(),
            'units' => OrganizationalUnit::query()->active()->orderBy('name')->get(),
            'leaveTypes' => LeaveType::query()->active()->orderBy('code')->get(),
            'year' => (int) request('year', $this->leaveReport->defaultYear()),
            'status' => request('status', LeaveRequest::STATUS_APPROVED),
            'summary' => $this->leaveReport->summary($user),
        ]);
    }

    public function leaveData(): JsonResponse
    {
        return (new LeaveSummaryDataTable(auth()->user(), $this->scope))->json();
    }

    public function leaveSummary(): JsonResponse
    {
        return response()->json($this->leaveReport->summary(auth()->user()));
    }

    public function payroll(): View
    {
        $user = auth()->user();
        $periods = $this->payrollReport->finalizedPeriods();
        $periodId = (int) request('payroll_period_id') ?: $this->payrollReport->defaultPeriodId();

        return view('reports.payroll', [
            'scopeLabel' => $this->scope->scopeLabel($user),
            'periods' => $periods,
            'periodId' => $periodId,
            'summary' => $this->payrollReport->summary($user, $periodId ?: null),
        ]);
    }

    public function payrollData(): JsonResponse
    {
        return (new PayrollSummaryDataTable(auth()->user(), $this->scope))->json();
    }

    public function payrollSummary(): JsonResponse
    {
        $periodId = (int) request('payroll_period_id') ?: null;

        return response()->json($this->payrollReport->summary(auth()->user(), $periodId));
    }
}
