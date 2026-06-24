<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\WorkLocation;

class AttendanceGeofenceService
{
    public function defaultLocation(): ?WorkLocation
    {
        return WorkLocation::query()
            ->active()
            ->where('is_default', true)
            ->whereNull('branch_id')
            ->first()
            ?? WorkLocation::query()->active()->whereNull('branch_id')->orderBy('id')->first()
            ?? WorkLocation::query()->active()->where('is_default', true)->first()
            ?? WorkLocation::query()->active()->orderBy('id')->first();
    }

    public function defaultLocationForBranch(int $branchId): ?WorkLocation
    {
        return WorkLocation::query()
            ->active()
            ->where('branch_id', $branchId)
            ->where('is_default', true)
            ->first()
            ?? WorkLocation::query()
                ->active()
                ->where('branch_id', $branchId)
                ->orderBy('id')
                ->first();
    }

    public function locationForEmployee(Employee $employee): ?WorkLocation
    {
        if ($employee->branch_id) {
            $branchLocation = $this->defaultLocationForBranch($employee->branch_id);

            if ($branchLocation) {
                return $branchLocation;
            }
        }

        return $this->defaultLocation();
    }

    public function distanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) ** 2
            + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }

    public function validateWithinGeofence(float $latitude, float $longitude, ?Employee $employee = null): array
    {
        $location = $employee
            ? $this->locationForEmployee($employee)
            : $this->defaultLocation();

        if (! $location) {
            throw new \InvalidArgumentException('Lokasi kerja belum dikonfigurasi. Hubungi HR.');
        }

        $distance = $this->distanceMeters(
            $latitude,
            $longitude,
            (float) $location->latitude,
            (float) $location->longitude
        );

        if ($distance > $location->radius_meters) {
            throw new \InvalidArgumentException(
                "Anda berada di luar area absen ({$distance} m dari {$location->name}, maksimal {$location->radius_meters} m)."
            );
        }

        return [
            'location' => $location,
            'distance_m' => $distance,
        ];
    }
}
