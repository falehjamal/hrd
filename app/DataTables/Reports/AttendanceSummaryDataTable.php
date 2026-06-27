<?php

namespace App\DataTables\Reports;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use App\Services\Reports\ReportScopeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AttendanceSummaryDataTable
{
    public function __construct(
        protected User $user,
        protected ReportScopeService $scope,
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_display', function ($row) {
                return e($row->employee_code).' — '.e($row->name);
            })
            ->addColumn('unit_display', fn ($row) => e($row->organizationalUnit?->name ?? '-'))
            ->addColumn('branch_display', fn ($row) => e($row->branch?->name ?? '-'))
            ->addColumn('present_count', fn ($row) => (int) ($row->present_count ?? 0))
            ->addColumn('late_count', fn ($row) => (int) ($row->late_count ?? 0))
            ->addColumn('absent_count', fn ($row) => (int) ($row->absent_count ?? 0))
            ->addColumn('leave_count', fn ($row) => (int) ($row->leave_count ?? 0))
            ->addColumn('half_day_count', fn ($row) => (int) ($row->half_day_count ?? 0))
            ->addColumn('total_recorded', function ($row) {
                return (int) ($row->present_count ?? 0)
                    + (int) ($row->late_count ?? 0)
                    + (int) ($row->absent_count ?? 0)
                    + (int) ($row->leave_count ?? 0)
                    + (int) ($row->half_day_count ?? 0);
            })
            ->filterColumn('employee_display', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('employees.name', 'like', "%{$keyword}%")
                        ->orWhere('employees.employee_code', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('unit_display', function ($query, $keyword) {
                $query->whereHas('organizationalUnit', fn ($q) => $q->where('name', 'like', "%{$keyword}%"));
            })
            ->filterColumn('branch_display', function ($query, $keyword) {
                $query->whereHas('branch', fn ($q) => $q->where('name', 'like', "%{$keyword}%"));
            })
            ->toJson();
    }

    protected function query(): Builder
    {
        $dateFrom = request('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = request('date_to', now()->endOfMonth()->format('Y-m-d'));

        $query = Employee::query()
            ->select('employees.*')
            ->selectRaw('COALESCE(SUM(CASE WHEN attendances.status = ? THEN 1 ELSE 0 END), 0) as present_count', [Attendance::STATUS_PRESENT])
            ->selectRaw('COALESCE(SUM(CASE WHEN attendances.status = ? THEN 1 ELSE 0 END), 0) as late_count', [Attendance::STATUS_LATE])
            ->selectRaw('COALESCE(SUM(CASE WHEN attendances.status = ? THEN 1 ELSE 0 END), 0) as absent_count', [Attendance::STATUS_ABSENT])
            ->selectRaw('COALESCE(SUM(CASE WHEN attendances.status = ? THEN 1 ELSE 0 END), 0) as leave_count', [Attendance::STATUS_LEAVE])
            ->selectRaw('COALESCE(SUM(CASE WHEN attendances.status = ? THEN 1 ELSE 0 END), 0) as half_day_count', [Attendance::STATUS_HALF_DAY])
            ->leftJoin('attendances', function ($join) use ($dateFrom, $dateTo) {
                $join->on('attendances.employee_id', '=', 'employees.id')
                    ->whereDate('attendances.date', '>=', $dateFrom)
                    ->whereDate('attendances.date', '<=', $dateTo);
            })
            ->with(['organizationalUnit', 'branch'])
            ->where('employees.status', 'active')
            ->groupBy('employees.id');

        $ids = $this->scope->scopedEmployeeIds($this->user);
        if ($ids === []) {
            $query->whereRaw('1 = 0');
        } elseif ($ids !== null) {
            $query->whereIn('employees.id', $ids);
        }

        if (request()->filled('branch_id')) {
            $query->where('employees.branch_id', request('branch_id'));
        }

        if (request()->filled('organizational_unit_id')) {
            $query->where('employees.organizational_unit_id', request('organizational_unit_id'));
        }

        $query->having(DB::raw('(
            COALESCE(SUM(CASE WHEN attendances.status = \''.Attendance::STATUS_PRESENT.'\' THEN 1 ELSE 0 END), 0) +
            COALESCE(SUM(CASE WHEN attendances.status = \''.Attendance::STATUS_LATE.'\' THEN 1 ELSE 0 END), 0) +
            COALESCE(SUM(CASE WHEN attendances.status = \''.Attendance::STATUS_ABSENT.'\' THEN 1 ELSE 0 END), 0) +
            COALESCE(SUM(CASE WHEN attendances.status = \''.Attendance::STATUS_LEAVE.'\' THEN 1 ELSE 0 END), 0) +
            COALESCE(SUM(CASE WHEN attendances.status = \''.Attendance::STATUS_HALF_DAY.'\' THEN 1 ELSE 0 END), 0)
        )'), '>', 0);

        return $query->orderBy('employees.name');
    }
}
