<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\EmployeeAccountCreated;
use Illuminate\Support\Str;

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
            'password' => $data['password'],
        ]);

        $employee->update(['user_id' => $user->id]);

        return $user;
    }

    /**
     * @return array{user: User, password: string}
     */
    public function createAutoForEmployee(Employee $employee): array
    {
        if ($employee->user_id) {
            throw new \InvalidArgumentException('Karyawan ini sudah memiliki akun login.');
        }

        $plainPassword = Str::password(10);

        $user = User::query()->create([
            'name' => $employee->name,
            'email' => $employee->email,
            'username' => $employee->employee_code,
            'password' => $plainPassword,
        ]);

        $employee->update(['user_id' => $user->id]);

        $user->notify(new EmployeeAccountCreated($employee, $plainPassword));

        return [
            'user' => $user,
            'password' => $plainPassword,
        ];
    }
}
