<?php

namespace App\Http\Controllers;

use App\DataTables\LeaveRequestDataTable;
use App\Http\Concerns\HandlesCrudModal;
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
    use HandlesCrudModal;

    public function __construct(
        protected LeaveRequestService $leaveRequestService
    ) {}

    protected function crudModalIndexRoute(): string
    {
        return 'leave-requests.index';
    }

    protected function crudModalResourceKey(): string
    {
        return '';
    }

    public function index(): View
    {
        $user = auth()->user();
        $isEmployee = ! $user->isHrUser();
        $leaveTypes = LeaveType::query()->active()->orderBy('code')->get(['id', 'code', 'name']);
        $employees = $user->isHrUser()
            ? Employee::query()->active()->orderBy('name')->get()
            : collect();

        return view('leave-requests.index', [
            'isEmployee' => $isEmployee,
            'leaveTypes' => $leaveTypes,
            'employees' => $employees,
            'linkedEmployee' => $user->employee,
        ]);
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

    public function create(): RedirectResponse
    {
        return $this->crudModalCreateRedirect();
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $user = $request->user();
        $employee = $user->isHrUser()
            ? Employee::query()->findOrFail($request->employee_id)
            : $user->employee;

        if (! $employee) {
            return redirect()->route('leave-requests.index')
                ->withInput()
                ->with('error', 'Karyawan tidak ditemukan.')
                ->with('open_crud_modal', 'create');
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
            return redirect()->route('leave-requests.index')
                ->withInput()
                ->with('error', $e->getMessage())
                ->with('open_crud_modal', 'create');
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
