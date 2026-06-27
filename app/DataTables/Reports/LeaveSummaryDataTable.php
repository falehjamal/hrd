<?php

namespace App\DataTables\Reports;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\Reports\ReportScopeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LeaveSummaryDataTable
{
    public function __construct(
        protected User $user,
        protected ReportScopeService $scope,
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('employee_display', function ($row) {
                return e($row->employee_code).' — '.e($row->employee_name);
            })
            ->addColumn('leave_type_display', fn ($row) => e($row->leave_type_code).' — '.e($row->leave_type_name))
            ->addColumn('total_days_display', fn ($row) => (int) $row->total_days.' hari')
            ->addColumn('request_count', fn ($row) => (int) $row->request_count)
            ->filterColumn('employee_display', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('employees.name', 'like', "%{$keyword}%")
                        ->orWhere('employees.employee_code', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('leave_type_display', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('leave_types.name', 'like', "%{$keyword}%")
                        ->orWhere('leave_types.code', 'like', "%{$keyword}%");
                });
            })
            ->toJson();
    }

    protected function query(): Builder
    {
        $year = (int) request('year', now()->year);

        $query = LeaveRequest::query()
            ->select([
                DB::raw("CONCAT(employees.id, '-', leave_types.id) as id"),
                'employees.id as employee_id',
                'employees.employee_code',
                'employees.name as employee_name',
                'leave_types.id as leave_type_id',
                'leave_types.code as leave_type_code',
                'leave_types.name as leave_type_name',
                DB::raw('SUM(leave_requests.total_days) as total_days'),
                DB::raw('COUNT(leave_requests.id) as request_count'),
            ])
            ->join('employees', 'employees.id', '=', 'leave_requests.employee_id')
            ->join('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
            ->where(function ($q) use ($year) {
                $q->whereYear('leave_requests.start_date', $year)
                    ->orWhereYear('leave_requests.end_date', $year);
            })
            ->when(request()->filled('status'), fn ($q) => $q->where('leave_requests.status', request('status')))
            ->when(request()->filled('leave_type_id'), fn ($q) => $q->where('leave_requests.leave_type_id', request('leave_type_id')))
            ->when(request()->filled('branch_id'), fn ($q) => $q->where('employees.branch_id', request('branch_id')))
            ->when(request()->filled('organizational_unit_id'), fn ($q) => $q->where('employees.organizational_unit_id', request('organizational_unit_id')))
            ->groupBy(
                'employees.id',
                'employees.employee_code',
                'employees.name',
                'leave_types.id',
                'leave_types.code',
                'leave_types.name',
            );

        $ids = $this->scope->scopedEmployeeIds($this->user);
        if ($ids === []) {
            $query->whereRaw('1 = 0');
        } elseif ($ids !== null) {
            $query->whereIn('employees.id', $ids);
        }

        return $query->orderBy('employees.name')->orderBy('leave_types.code');
    }
}
