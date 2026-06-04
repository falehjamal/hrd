<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    public const LATE_TOLERANCE_MINUTES = 15;

    public function __construct(
        protected AttendanceGeofenceService $geofence
    ) {}

    public function storeManual(array $data, ?UploadedFile $checkInPhoto, ?UploadedFile $checkOutPhoto, int $userId): Attendance
    {
        $this->ensureUniqueDate((int) $data['employee_id'], $data['date']);

        $employee = Employee::query()->with('shift')->findOrFail($data['employee_id']);
        $checkInAt = $this->parseDateTime($data['date'], $data['check_in_time'] ?? null);
        $checkOutAt = $this->parseDateTime($data['date'], $data['check_out_time'] ?? null);

        $status = $data['status'] ?? $this->resolveStatus($employee, $checkInAt);

        $attendance = Attendance::query()->create([
            'employee_id' => $employee->id,
            'date' => $data['date'],
            'check_in_at' => $checkInAt,
            'check_out_at' => $checkOutAt,
            'source' => Attendance::SOURCE_MANUAL,
            'status' => $status,
            'notes' => $data['notes'] ?? null,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        if ($checkInPhoto) {
            $attendance->update([
                'check_in_photo_path' => $this->storePhoto($attendance, 'check-in', $checkInPhoto),
            ]);
        }

        if ($checkOutPhoto) {
            $attendance->update([
                'check_out_photo_path' => $this->storePhoto($attendance, 'check-out', $checkOutPhoto),
            ]);
        }

        return $attendance->fresh();
    }

    public function updateManual(Attendance $attendance, array $data, ?UploadedFile $checkInPhoto, ?UploadedFile $checkOutPhoto, int $userId): Attendance
    {
        $employeeId = (int) ($data['employee_id'] ?? $attendance->employee_id);
        $date = $data['date'] ?? $attendance->date->format('Y-m-d');

        if ($employeeId !== $attendance->employee_id || $date !== $attendance->date->format('Y-m-d')) {
            $this->ensureUniqueDate($employeeId, $date, $attendance->id);
        }

        $employee = Employee::query()->with('shift')->findOrFail($employeeId);
        $checkInAt = $this->parseDateTime($date, $data['check_in_time'] ?? null);
        $checkOutAt = $this->parseDateTime($date, $data['check_out_time'] ?? null);
        $status = $data['status'] ?? $this->resolveStatus($employee, $checkInAt);

        $attendance->update([
            'employee_id' => $employeeId,
            'date' => $date,
            'check_in_at' => $checkInAt,
            'check_out_at' => $checkOutAt,
            'status' => $status,
            'notes' => $data['notes'] ?? $attendance->notes,
            'updated_by' => $userId,
        ]);

        if ($checkInPhoto) {
            $this->deletePhoto($attendance->check_in_photo_path);
            $attendance->update([
                'check_in_photo_path' => $this->storePhoto($attendance, 'check-in', $checkInPhoto),
            ]);
        }

        if ($checkOutPhoto) {
            $this->deletePhoto($attendance->check_out_photo_path);
            $attendance->update([
                'check_out_photo_path' => $this->storePhoto($attendance, 'check-out', $checkOutPhoto),
            ]);
        }

        return $attendance->fresh();
    }

    public function checkInGps(Employee $employee, float $latitude, float $longitude, UploadedFile $photo, int $userId): Attendance
    {
        $geofence = $this->geofence->validateWithinGeofence($latitude, $longitude);
        $today = today()->format('Y-m-d');

        $attendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance?->check_in_at && $attendance?->check_out_at) {
            throw new \InvalidArgumentException('Absensi hari ini sudah lengkap (masuk dan pulang).');
        }

        if ($attendance?->check_in_at && ! $attendance->check_out_at) {
            throw new \InvalidArgumentException('Anda sudah absen masuk. Gunakan absen pulang.');
        }

        $employee->load('shift');
        $now = now();
        $status = $this->resolveStatus($employee, $now);

        if ($attendance) {
            $attendance->update([
                'check_in_at' => $now,
                'check_in_latitude' => $latitude,
                'check_in_longitude' => $longitude,
                'check_in_distance_m' => $geofence['distance_m'],
                'source' => Attendance::SOURCE_GPS,
                'status' => $status,
                'updated_by' => $userId,
            ]);
        } else {
            $attendance = Attendance::query()->create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_in_at' => $now,
                'check_in_latitude' => $latitude,
                'check_in_longitude' => $longitude,
                'check_in_distance_m' => $geofence['distance_m'],
                'source' => Attendance::SOURCE_GPS,
                'status' => $status,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        }

        $this->deletePhoto($attendance->check_in_photo_path);
        $attendance->update([
            'check_in_photo_path' => $this->storePhoto($attendance, 'check-in', $photo),
        ]);

        return $attendance->fresh();
    }

    public function checkOutGps(Employee $employee, float $latitude, float $longitude, ?UploadedFile $photo, int $userId): Attendance
    {
        $this->geofence->validateWithinGeofence($latitude, $longitude);

        $attendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->first();

        if (! $attendance?->check_in_at) {
            throw new \InvalidArgumentException('Anda belum absen masuk hari ini.');
        }

        if ($attendance->check_out_at) {
            throw new \InvalidArgumentException('Anda sudah absen pulang hari ini.');
        }

        $attendance->update([
            'check_out_at' => now(),
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'updated_by' => $userId,
        ]);

        if ($photo) {
            $this->deletePhoto($attendance->check_out_photo_path);
            $attendance->update([
                'check_out_photo_path' => $this->storePhoto($attendance, 'check-out', $photo),
            ]);
        }

        return $attendance->fresh();
    }

    public function resolveStatus(?Employee $employee, ?Carbon $checkInAt): string
    {
        if (! $checkInAt) {
            return Attendance::STATUS_ABSENT;
        }

        if (! $employee?->shift) {
            return Attendance::STATUS_PRESENT;
        }

        $shiftStart = Carbon::parse($checkInAt->format('Y-m-d').' '.$employee->shift->start_time);
        $lateThreshold = $shiftStart->copy()->addMinutes(self::LATE_TOLERANCE_MINUTES);

        return $checkInAt->greaterThan($lateThreshold)
            ? Attendance::STATUS_LATE
            : Attendance::STATUS_PRESENT;
    }

    public function canViewPhoto(Attendance $attendance, User $user): bool
    {
        $linked = $user->employee;

        if (! $linked) {
            return true;
        }

        return $attendance->employee_id === $linked->id;
    }

    public function deleteAttendance(Attendance $attendance): void
    {
        $this->deletePhoto($attendance->check_in_photo_path);
        $this->deletePhoto($attendance->check_out_photo_path);
        $attendance->delete();
    }

    protected function ensureUniqueDate(int $employeeId, string $date, ?int $exceptId = null): void
    {
        $exists = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists();

        if ($exists) {
            throw new \InvalidArgumentException('Absensi untuk karyawan dan tanggal ini sudah ada.');
        }
    }

    protected function parseDateTime(string $date, ?string $time): ?Carbon
    {
        if (! $time) {
            return null;
        }

        return Carbon::parse($date.' '.$time);
    }

    protected function storePhoto(Attendance $attendance, string $type, UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $path = "attendances/{$attendance->id}/{$type}.{$extension}";

        Storage::disk('local')->put($path, $file->get());

        return $path;
    }

    protected function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
