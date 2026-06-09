<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\EmployeeAccountNotification;
use Illuminate\Support\Str;

class EmployeeAccountService
{
    /**
     * @return array{user: User, password: string}
     */
    public function createAutoForEmployee(
        Employee $employee,
        ?string $username = null,
        ?string $password = null,
        bool $sendNotification = true,
    ): array {
        if ($employee->user_id) {
            throw new \InvalidArgumentException('Karyawan ini sudah memiliki akun login.');
        }

        $plainPassword = $this->resolvePassword($password);
        $resolvedUsername = $this->resolveUsername($username, $employee->email);

        $user = User::query()->create([
            'name' => $employee->name,
            'email' => $employee->email,
            'username' => $resolvedUsername,
            'password' => $plainPassword,
        ]);

        $employee->update(['user_id' => $user->id]);

        $this->sendNotification($user, $employee, 'created', $plainPassword, $sendNotification);

        return [
            'user' => $user,
            'password' => $plainPassword,
        ];
    }

    public function createOrSyncForEmployee(
        Employee $employee,
        ?string $username = null,
        ?string $password = null,
        bool $sendNotification = true,
    ): void {
        if ($employee->user_id) {
            $this->syncForEmployee($employee, [
                'name' => $employee->name,
                'email' => $employee->email,
                'username' => $username ?? $employee->user?->username,
                'password' => $password,
            ], $sendNotification);

            return;
        }

        $this->createAutoForEmployee($employee, $username, $password, $sendNotification);
    }

    public function syncForEmployee(Employee $employee, array $data, bool $sendNotification = true): void
    {
        $user = $employee->user;

        if (! $user) {
            return;
        }

        if (filled($data['username'] ?? null)) {
            $resolvedUsername = $this->resolveUsername($data['username'], $data['email'], $user->id);
        } elseif ($user->username) {
            $resolvedUsername = $user->username;
        } else {
            $resolvedUsername = $this->resolveUsername(null, $data['email'], $user->id);
        }

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $resolvedUsername,
        ];

        $plainPassword = null;

        if (filled($data['password'] ?? null)) {
            $plainPassword = $data['password'];
            $userData['password'] = $plainPassword;
        }

        $user->update($userData);

        $this->sendNotification($user, $employee->refresh(), 'updated', $plainPassword, $sendNotification);
    }

    private function sendNotification(
        User $user,
        Employee $employee,
        string $action,
        ?string $plainPassword,
        bool $sendNotification,
    ): void {
        if (! $sendNotification) {
            return;
        }

        $user->notify(new EmployeeAccountNotification($employee, $action, $plainPassword));
    }

    private function resolveUsername(?string $input, string $email, ?int $ignoreUserId = null): string
    {
        $base = filled($input)
            ? strtolower(trim($input))
            : strtolower(Str::before($email, '@'));

        $candidate = $base;
        $suffix = 2;

        while ($this->usernameExists($candidate, $ignoreUserId)) {
            $candidate = $base.$suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function resolvePassword(?string $input): string
    {
        return filled($input) ? $input : '1234';
    }

    private function usernameExists(string $username, ?int $ignoreUserId = null): bool
    {
        return User::query()
            ->when($ignoreUserId, fn ($query) => $query->where('id', '!=', $ignoreUserId))
            ->where('username', $username)
            ->exists();
    }
}
