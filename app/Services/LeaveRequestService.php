<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeWeeklyShift;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class LeaveRequestService
{
    public function __construct(
        protected EmployeeShiftResolverService $shiftResolver,
        protected EmployeeLeaveBalanceService $balanceService
    ) {}

    public function calculateLeaveDays(Employee $employee, Carbon|string $startDate, Carbon|string $endDate): int
    {
        return $this->getWorkingDates($employee, $startDate, $endDate)->count();
    }

    public function calculateLeaveDaysForYear(Employee $employee, Carbon|string $startDate, Carbon|string $endDate, int $year): int
    {
        return $this->getWorkingDates($employee, $startDate, $endDate)
            ->filter(fn (Carbon $date) => $date->year === $year)
            ->count();
    }

    public function getWorkingDates(Employee $employee, Carbon|string $startDate, Carbon|string $endDate): Collection
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($end->lessThan($start)) {
            return collect();
        }

        $dates = collect();

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($this->isWorkingDay($employee, $date)) {
                $dates->push($date->copy());
            }
        }

        return $dates;
    }

    public function validateSubmission(
        Employee $employee,
        int $leaveTypeId,
        Carbon|string $startDate,
        Carbon|string $endDate,
        ?int $exceptRequestId = null
    ): int {
        if ($employee->status !== 'active') {
            throw new \InvalidArgumentException('Karyawan tidak aktif.');
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($end->lessThan($start)) {
            throw new \InvalidArgumentException('Tanggal selesai harus setelah atau sama dengan tanggal mulai.');
        }

        $leaveType = LeaveType::query()->active()->find($leaveTypeId);

        if (! $leaveType) {
            throw new \InvalidArgumentException('Jenis cuti tidak valid atau tidak aktif.');
        }

        $totalDays = $this->calculateLeaveDays($employee, $start, $end);

        if ($totalDays < 1) {
            throw new \InvalidArgumentException('Rentang tanggal tidak memiliki hari kerja yang valid.');
        }

        $overlapExists = LeaveRequest::query()
            ->where('employee_id', $employee->id)
            ->when($exceptRequestId, fn ($q) => $q->where('id', '!=', $exceptRequestId))
            ->whereIn('status', [LeaveRequest::STATUS_PENDING, LeaveRequest::STATUS_APPROVED])
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start)
            ->exists();

        if ($overlapExists) {
            throw new \InvalidArgumentException('Sudah ada pengajuan cuti yang bertabrakan pada rentang tanggal ini.');
        }

        foreach (range($start->year, $end->year) as $year) {
            $daysInYear = $this->calculateLeaveDaysForYear($employee, $start, $end, $year);

            if ($daysInYear < 1) {
                continue;
            }

            $balance = $this->balanceService->getOrCreateBalance($employee, $leaveTypeId, $year);

            if ($balance->remaining_days < $daysInYear) {
                throw new \InvalidArgumentException(
                    "Saldo cuti {$leaveType->name} tahun {$year} tidak mencukupi (butuh {$daysInYear} hari, sisa {$balance->remaining_days} hari)."
                );
            }
        }

        return $totalDays;
    }

    public function submit(
        Employee $employee,
        int $leaveTypeId,
        Carbon|string $startDate,
        Carbon|string $endDate,
        string $reason,
        ?UploadedFile $attachment = null
    ): LeaveRequest {
        $totalDays = $this->validateSubmission($employee, $leaveTypeId, $startDate, $endDate);

        $request = LeaveRequest::query()->create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveTypeId,
            'start_date' => Carbon::parse($startDate)->toDateString(),
            'end_date' => Carbon::parse($endDate)->toDateString(),
            'total_days' => $totalDays,
            'reason' => $reason,
            'status' => LeaveRequest::STATUS_PENDING,
        ]);

        if ($attachment) {
            $request->update([
                'attachment_path' => $this->storeAttachment($request, $attachment),
            ]);
        }

        return $request->fresh(['leaveType', 'employee']);
    }

    public function approve(LeaveRequest $leaveRequest, User $user): void
    {
        if ($leaveRequest->status !== LeaveRequest::STATUS_PENDING) {
            throw new \InvalidArgumentException('Pengajuan ini sudah diproses.');
        }

        $leaveRequest->load(['employee', 'leaveType']);

        $this->validateSubmission(
            $leaveRequest->employee,
            $leaveRequest->leave_type_id,
            $leaveRequest->start_date,
            $leaveRequest->end_date,
            $leaveRequest->id
        );

        $workingDates = $this->getWorkingDates(
            $leaveRequest->employee,
            $leaveRequest->start_date,
            $leaveRequest->end_date
        );

        foreach ($workingDates as $date) {
            $existing = Attendance::query()
                ->where('employee_id', $leaveRequest->employee_id)
                ->whereDate('date', $date)
                ->first();

            if ($existing && $existing->status !== Attendance::STATUS_LEAVE) {
                throw new \InvalidArgumentException(
                    'Tidak dapat menyetujui: sudah ada absensi '.$date->format('d/m/Y').' dengan status '.attendance_status_label($existing->status).'.'
                );
            }
        }

        foreach ($workingDates as $date) {
            $shift = $this->shiftResolver->shiftForDate($leaveRequest->employee, $date);
            $notes = 'Cuti: '.$leaveRequest->leaveType->name;

            $existing = Attendance::query()
                ->where('employee_id', $leaveRequest->employee_id)
                ->whereDate('date', $date)
                ->first();

            if ($existing) {
                $existing->update([
                    'shift_id' => $shift?->id,
                    'status' => Attendance::STATUS_LEAVE,
                    'leave_request_id' => $leaveRequest->id,
                    'notes' => $notes,
                    'source' => Attendance::SOURCE_MANUAL,
                    'updated_by' => $user->id,
                ]);
            } else {
                Attendance::query()->create([
                    'employee_id' => $leaveRequest->employee_id,
                    'shift_id' => $shift?->id,
                    'date' => $date->toDateString(),
                    'source' => Attendance::SOURCE_MANUAL,
                    'status' => Attendance::STATUS_LEAVE,
                    'leave_request_id' => $leaveRequest->id,
                    'notes' => $notes,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        }

        $leaveRequest->update([
            'status' => LeaveRequest::STATUS_APPROVED,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_notes' => null,
        ]);

        foreach (range($leaveRequest->start_date->year, $leaveRequest->end_date->year) as $year) {
            $balance = $this->balanceService->getBalance(
                $leaveRequest->employee,
                $leaveRequest->leave_type_id,
                $year
            );

            if ($balance) {
                $this->balanceService->syncUsedDays($balance);
            }
        }
    }

    public function reject(LeaveRequest $leaveRequest, User $user, string $rejectionNotes): void
    {
        if ($leaveRequest->status !== LeaveRequest::STATUS_PENDING) {
            throw new \InvalidArgumentException('Pengajuan ini sudah diproses.');
        }

        $leaveRequest->update([
            'status' => LeaveRequest::STATUS_REJECTED,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_notes' => $rejectionNotes,
        ]);
    }

    public function deletePending(LeaveRequest $leaveRequest, User $user): void
    {
        if ($leaveRequest->status !== LeaveRequest::STATUS_PENDING) {
            throw new \InvalidArgumentException('Hanya pengajuan menunggu yang dapat dihapus.');
        }

        if (! $user->isHrUser() && $leaveRequest->employee_id !== $user->employee?->id) {
            abort(403);
        }

        if ($leaveRequest->attachment_path) {
            Storage::disk('local')->delete($leaveRequest->attachment_path);
        }

        $leaveRequest->delete();
    }

    protected function isWorkingDay(Employee $employee, Carbon $date): bool
    {
        if ($this->shiftResolver->isCompanyHoliday($date)) {
            return false;
        }

        if ($this->shiftResolver->isDayOff($employee, $date)) {
            return false;
        }

        $weekly = EmployeeWeeklyShift::query()
            ->where('employee_id', $employee->id)
            ->where('day_of_week', $date->isoWeekday())
            ->first();

        if ($weekly && $weekly->shift_id === null) {
            return false;
        }

        return $this->shiftResolver->shiftForDate($employee, $date) !== null;
    }

    protected function storeAttachment(LeaveRequest $request, UploadedFile $file): string
    {
        return $file->store("leave-requests/{$request->id}", 'local');
    }
}
