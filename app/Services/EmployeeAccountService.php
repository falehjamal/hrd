<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeAccountService
{
    public function createForEmployee(Employee $employee, array $data): User
    {
        if ($employee->user_id) {
            throw new \InvalidArgumentException('Karyawan ini sudah memiliki akun login.');
        }

        $user = User::query()->create([
            'name' => $employee->name,
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);

        $employee->update(['user_id' => $user->id]);

        return $user;
    }
}
