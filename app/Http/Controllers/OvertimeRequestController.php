<?php

namespace App\Http\Controllers;

use App\DataTables\OvertimeRequestDataTable;
use App\Http\Concerns\HandlesCrudModal;
use App\Http\Requests\RejectOvertimeRequest;
use App\Http\Requests\StoreOvertimeRequest;
use App\Models\Employee;
use App\Models\OvertimeRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OvertimeRequestController extends Controller
{
    use HandlesCrudModal;

    protected function crudModalIndexRoute(): string
    {
        return 'overtime-requests.index';
    }

    protected function crudModalResourceKey(): string
    {
        return '';
    }

    public function index(): View
    {
        $user = auth()->user();
        $isEmployee = ! $user->isHrUser();
        $employees = $user->isHrUser()
            ? Employee::query()->active()->orderBy('name')->get()
            : collect();

        return view('overtime-requests.index', [
            'isEmployee' => $isEmployee,
            'employees' => $employees,
            'linkedEmployee' => $user->employee,
        ]);
    }

    public function data(): JsonResponse
    {
        $employeeId = auth()->user()->isHrUser()
            ? null
            : auth()->user()->employee?->id;

        return (new OvertimeRequestDataTable($employeeId))->json();
    }

    public function create(): RedirectResponse
    {
        return $this->crudModalCreateRedirect();
    }

    public function store(StoreOvertimeRequest $request): RedirectResponse
    {
        $user = $request->user();
        $employeeId = $user->isHrUser()
            ? (int) $request->employee_id
            : (int) $user->employee?->id;

        $start = Carbon::parse($request->date.' '.$request->start_time);
        $end = Carbon::parse($request->date.' '.$request->end_time);

        if ($end->lessThanOrEqualTo($start)) {
            return redirect()->route('overtime-requests.index')
                ->withInput()
                ->with('error', 'Jam selesai harus setelah jam mulai.')
                ->with('open_crud_modal', 'create');
        }

        $pendingExists = OvertimeRequest::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', $request->date)
            ->where('status', OvertimeRequest::STATUS_PENDING)
            ->exists();

        if ($pendingExists) {
            return redirect()->route('overtime-requests.index')
                ->withInput()
                ->with('error', 'Sudah ada pengajuan lembur menunggu untuk tanggal ini.')
                ->with('open_crud_modal', 'create');
        }

        OvertimeRequest::query()->create([
            'employee_id' => $employeeId,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => (int) $start->diffInMinutes($end),
            'reason' => $request->reason,
            'status' => OvertimeRequest::STATUS_PENDING,
        ]);

        return redirect()->route('overtime-requests.index')->with('success', 'Pengajuan lembur berhasil dikirim.');
    }

    public function approve(OvertimeRequest $overtimeRequest): RedirectResponse
    {
        $this->ensureHr();

        if ($overtimeRequest->status !== OvertimeRequest::STATUS_PENDING) {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $overtimeRequest->update([
            'status' => OvertimeRequest::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_notes' => null,
        ]);

        return back()->with('success', 'Lembur disetujui.');
    }

    public function reject(RejectOvertimeRequest $request, OvertimeRequest $overtimeRequest): RedirectResponse
    {
        $this->ensureHr();

        if ($overtimeRequest->status !== OvertimeRequest::STATUS_PENDING) {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $overtimeRequest->update([
            'status' => OvertimeRequest::STATUS_REJECTED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_notes' => $request->rejection_notes,
        ]);

        return back()->with('success', 'Lembur ditolak.');
    }

    public function destroy(OvertimeRequest $overtimeRequest): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->isHrUser() && $overtimeRequest->employee_id !== $user->employee?->id) {
            abort(403);
        }

        if ($overtimeRequest->status !== OvertimeRequest::STATUS_PENDING) {
            return back()->with('error', 'Hanya pengajuan menunggu yang dapat dihapus.');
        }

        $overtimeRequest->delete();

        return back()->with('success', 'Pengajuan lembur dihapus.');
    }

    protected function ensureHr(): void
    {
        abort_unless(auth()->user()->isHrUser(), 403, 'Hanya HR yang dapat menyetujui lembur.');
    }
}
