<?php

namespace Database\Seeders;

use App\Models\Central\TenantUser;
use App\Models\CompanyHoliday;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\EmployeeShiftOverride;
use App\Models\EmployeeWeeklyShift;
use App\Models\Shift;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkLocation;
use App\Services\EmployeeAccountService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    private const DEMO_TENANT_ID = 'demo';

    public function run(): void
    {
        $tenant = Tenant::query()->find(self::DEMO_TENANT_ID);

        if ($tenant) {
            $tenant->update([
                'app_title' => 'HRD Demo',
                'status' => Tenant::STATUS_ACTIVE,
            ]);
        }

        if (! $tenant) {
            // migrate:fresh menghapus record di central, tapi DB tenant lama masih ada.
            $this->dropTenantDatabaseIfExists(self::DEMO_TENANT_ID);

            $tenant = Tenant::query()->create([
                'id' => self::DEMO_TENANT_ID,
                'name' => 'Demo Company',
                'slug' => 'demo',
                'app_title' => 'HRD Demo',
                'status' => Tenant::STATUS_ACTIVE,
            ]);
        }

        Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id],
            '--force' => true,
        ]);

        tenancy()->initialize($tenant);

        $user = User::query()->firstOrCreate(
            ['email' => 'admin@hrd.test'],
            [
                'name' => 'Admin HRD',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        TenantUser::query()->updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'email' => $user->email,
            ],
            [
                'username' => $user->username,
            ]
        );

        $this->migrateLegacyUsers($tenant);
        $this->seedMasterData();
        $this->seedOperationalData();
        $this->seedShiftSchedule();

        tenancy()->end();
    }

    protected function migrateLegacyUsers(Tenant $tenant): void
    {
        $legacyDatabase = 'hrd';

        try {
            $schemaExists = DB::connection('central')->selectOne(
                'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?',
                [$legacyDatabase]
            );

            if (! $schemaExists) {
                return;
            }

            $legacyUsers = DB::connection('central')->select(
                "SELECT name, email, password, email_verified_at FROM `{$legacyDatabase}`.`users`"
            );

            foreach ($legacyUsers as $legacyUser) {
                if ($legacyUser->email === 'admin@hrd.test') {
                    continue;
                }

                if (User::query()->where('email', $legacyUser->email)->exists()) {
                    continue;
                }

                $userId = User::query()->insertGetId([
                    'name' => $legacyUser->name,
                    'email' => $legacyUser->email,
                    'username' => null,
                    'password' => $legacyUser->password,
                    'email_verified_at' => $legacyUser->email_verified_at,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $imported = User::query()->find($userId);

                TenantUser::query()->updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'email' => $imported->email,
                    ],
                    [
                        'username' => $imported->username,
                    ]
                );
            }
        } catch (\Throwable) {
            // Legacy DB tidak tersedia — abaikan.
        }
    }

    protected function seedMasterData(): void
    {
        if (Shift::query()->exists()) {
            return;
        }

        $shiftPagi = Shift::query()->create([
            'code' => 'PAGI',
            'name' => 'Shift Pagi',
            'start_time' => '08:00',
            'end_time' => '17:00',
            'break_minutes' => 60,
            'is_active' => true,
        ]);

        $shiftSiang = Shift::query()->create([
            'code' => 'SIANG',
            'name' => 'Shift Siang',
            'start_time' => '14:00',
            'end_time' => '22:00',
            'break_minutes' => 60,
            'is_active' => true,
        ]);

        $shiftMalam = Shift::query()->create([
            'code' => 'MALAM',
            'name' => 'Shift Malam',
            'start_time' => '22:00',
            'end_time' => '06:00',
            'break_minutes' => 60,
            'is_active' => true,
        ]);

        $employees = [
            ['employee_code' => 'EMP001', 'name' => 'Budi Santoso', 'department' => 'HR', 'position' => 'Staff HR', 'shift_id' => $shiftPagi->id, 'basic' => 5500000, 'allowance' => 500000],
            ['employee_code' => 'EMP002', 'name' => 'Siti Rahayu', 'department' => 'Keuangan', 'position' => 'Akuntan', 'shift_id' => $shiftPagi->id, 'basic' => 6000000, 'allowance' => 750000],
            ['employee_code' => 'EMP003', 'name' => 'Andi Wijaya', 'department' => 'Operasional', 'position' => 'Supervisor', 'shift_id' => $shiftSiang->id, 'basic' => 7000000, 'allowance' => 1000000],
            ['employee_code' => 'EMP004', 'name' => 'Dewi Lestari', 'department' => 'IT', 'position' => 'Developer', 'shift_id' => $shiftPagi->id, 'basic' => 8000000, 'allowance' => 1500000],
            ['employee_code' => 'EMP005', 'name' => 'Rizki Pratama', 'department' => 'Gudang', 'position' => 'Staff Gudang', 'shift_id' => $shiftMalam->id, 'basic' => 4800000, 'allowance' => 400000],
        ];

        foreach ($employees as $data) {
            $employee = Employee::query()->create([
                'employee_code' => $data['employee_code'],
                'name' => $data['name'],
                'email' => strtolower(str_replace(' ', '.', $data['name'])).'@demo.test',
                'department' => $data['department'],
                'position' => $data['position'],
                'shift_id' => $data['shift_id'],
                'join_date' => now()->subMonths(rand(6, 36)),
                'status' => 'active',
            ]);

            EmployeeSalary::query()->create([
                'employee_id' => $employee->id,
                'effective_date' => now()->startOfYear(),
                'basic_salary' => $data['basic'],
                'fixed_allowance' => $data['allowance'],
                'is_active' => true,
                'notes' => 'Gaji awal seed demo',
            ]);

            if (! $employee->user_id) {
                app(EmployeeAccountService::class)->createAutoForEmployee($employee, sendNotification: false);
            }
        }
    }

    protected function seedOperationalData(): void
    {
        if (! WorkLocation::query()->exists()) {
            WorkLocation::query()->create([
                'name' => 'Kantor Pusat Demo',
                'latitude' => -6.2000000,
                'longitude' => 106.8166667,
                'radius_meters' => 200,
                'is_active' => true,
                'is_default' => true,
            ]);
        }

    }

    protected function seedShiftSchedule(): void
    {
        $employee = Employee::query()->where('employee_code', 'EMP003')->first();
        $shiftSiang = Shift::query()->where('code', 'SIANG')->first();
        $shiftMalam = Shift::query()->where('code', 'MALAM')->first();

        if (! $employee || ! $shiftSiang) {
            return;
        }

        foreach (range(1, 5) as $day) {
            EmployeeWeeklyShift::query()->updateOrCreate(
                ['employee_id' => $employee->id, 'day_of_week' => $day],
                ['shift_id' => $shiftSiang->id]
            );
        }

        if ($shiftMalam) {
            EmployeeWeeklyShift::query()->updateOrCreate(
                ['employee_id' => $employee->id, 'day_of_week' => 6],
                ['shift_id' => $shiftMalam->id]
            );
        }

        EmployeeShiftOverride::query()->firstOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => now()->addDays(3)->toDateString(),
            ],
            [
                'shift_id' => null,
                'notes' => 'Libur demo (override)',
            ]
        );

        CompanyHoliday::query()->firstOrCreate(
            ['date' => now()->startOfMonth()->addDays(14)->toDateString()],
            [
                'name' => 'Libur Nasional Demo',
                'notes' => 'Contoh libur perusahaan',
                'is_active' => true,
            ]
        );

        CompanyHoliday::query()->firstOrCreate(
            ['date' => now()->startOfMonth()->addDays(27)->toDateString()],
            [
                'name' => 'Cuti Bersama Demo',
                'is_active' => true,
            ]
        );
    }

    protected function dropTenantDatabaseIfExists(string $tenantId): void
    {
        $database = config('tenancy.database.prefix').$tenantId;

        DB::connection('central')->statement(
            'DROP DATABASE IF EXISTS `'.str_replace('`', '``', $database).'`'
        );
    }
}
