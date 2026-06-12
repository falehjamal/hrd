<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EmployeePhotoService
{
    public function storePhoto(Employee $employee, UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $path = "employees/{$employee->id}/photo.{$extension}";

        Storage::disk('local')->put($path, $file->get());

        return $path;
    }

    public function replacePhoto(Employee $employee, UploadedFile $file): string
    {
        $this->deletePhoto($employee->photo_path);

        return $this->storePhoto($employee, $file);
    }

    public function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }

    public function canViewPhoto(Employee $employee, User $user): bool
    {
        if ($user->isHrUser()) {
            return true;
        }

        $linked = $user->employee;

        return $linked && $linked->id === $employee->id;
    }
}
