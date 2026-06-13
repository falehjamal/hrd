<?php

namespace App\DataTables;

use App\Models\EmployeeLeaveBalance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class EmployeeLeaveBalanceDataTable
{
    public function __construct(
        protected int $employeeId,
        protected int $year
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('type_display', fn (EmployeeLeaveBalance $row) => e($row->leaveType->code).' — '.e($row->leaveType->name))
            ->addColumn('remaining_display', fn (EmployeeLeaveBalance $row) => $row->remaining_days.' hari')
            ->toJson();
    }

    protected function query(): Builder
    {
        return EmployeeLeaveBalance::query()
            ->with('leaveType')
            ->where('employee_id', $this->employeeId)
            ->where('year', $this->year)
            ->join('leave_types', 'leave_types.id', '=', 'employee_leave_balances.leave_type_id')
            ->orderBy('leave_types.code')
            ->select('employee_leave_balances.*');
    }
}
