<?php

namespace App\Http\Controllers;

use App\DataTables\LeaveRequestDataTable;
use App\Http\Requests\RejectLeaveRequest;
use App\Http\Requests\StoreLeaveRequest;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function __construct(
        protected LeaveRequestService $leaveRequestService
    ) {}

    public function index(): View
    {
        $isEmployee = ! auth()->user()->isHrUser();
        $leaveTypes = LeaveType::query()->active()->orderBy('code')->get(['id', 'code', 'name']);

        return view('leave-requests.index', compact('isEmployee', 'leaveTypes'));
    }

    public function data(): JsonResponse
    {
        $employeeId = auth()->user()->isHrUser()
            ? null
            : auth()->user()->employee?->id;

        return (new LeaveRequestDataTable($employeeId))->json();
    }

    public function dataForEmployee(Employee $employee): JsonResponse
    {
        return (new LeaveRequestDataTable($employee->id))->json();
    }

    public function create(): View
    {
        $user = auth()->user();
        $employees = $user->isHrUser()
            ? Employee::query()->active()->orderBy('name')->get()
            : collect();
        $leaveTypes = LeaveType::query()->active()->orderBy('code')->get();

        return view('leave-requests.create', [
            'employees' => $employees,
            'linkedEmployee' => $user->employee,
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $user = $request->user();
        $employee = $user->isHrUser()
            ? Employee::query()->findOrFail($request->employee_id)
            : $user->employee;

        if (! $employee) {
            return back()->withInput()->with('error', 'Karyawan tidak ditemukan.');
        }

        try {
            $this->leaveRequestService->submit(
                $employee,
                (int) $request->leave_type_id,
                $request->start_date,
                $request->end_date,
                $request->reason,
                $request->file('attachment')
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('leave-requests.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function calculateDays(Request $request): JsonResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $employee = Employee::query()->findOrFail($data['employee_id']);
        $days = $this->leaveRequestService->calculateLeaveDays(
            $employee,
            $data['start_date'],
            $data['end_date']
        );

        return response()->json(['total_days' => $days]);
    }

    public function approve(LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->ensureHr();

        try {
            $this->leaveRequestService->approve($leaveRequest, auth()->user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan cuti disetujui.');
    }

    public function reject(RejectLeaveRequest $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->ensureHr();

        try {
            $this->leaveRequestService->reject($leaveRequest, auth()->user(), $request->rejection_notes);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan cuti ditolak.');
    }

    public function destroy(LeaveRequest $leaveRequest): RedirectResponse
    {
        try {
            $this->leaveRequestService->deletePending($leaveRequest, auth()->user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan cuti dihapus.');
    }

    protected function ensureHr(): void
    {
        abort_unless(auth()->user()->isHrUser(), 403, 'Hanya HR yang dapat memproses pengajuan cuti.');
    }
}
