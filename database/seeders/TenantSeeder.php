<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Central\TenantUser;
use App\Models\CompanyHoliday;
use App\Models\DeductionType;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeLoan;
use App\Models\EmployeeSalary;
use App\Models\EmployeeShiftOverride;
use App\Models\EmployeeWeeklyShift;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\OrganizationalUnit;
use App\Models\Position;
use App\Models\Shift;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkLocation;
use App\Services\EmployeeAccountService;
use App\Services\EmployeeLeaveBalanceService;
use App\Services\EmployeeLoanService;
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

        $adminUser = User::query()->firstOrCreate(
            ['email' => 'admin@hrd.test'],
            [
                'name' => 'Admin HRD',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'hr',
            ]
        );

        $adminUser->update(['role' => 'hr']);

        TenantUser::query()->updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'email' => $adminUser->email,
            ],
            [
                'username' => $adminUser->username,
            ]
        );

        $this->migrateLegacyUsers($tenant);
        $this->seedShifts();
        $orgData = $this->seedOrganizationData();
        $branches = $this->seedBranches();
        $this->seedEmployees($adminUser, $orgData, $branches);
        $this->seedOperationalData($branches);
        $this->seedShiftSchedule();
        $this->seedLeaveData();
        $this->seedDeductionAndLoanData();

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
                    'role' => 'employee',
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

    protected function seedShifts(): void
    {
        if (Shift::query()->exists()) {
            return;
        }

        Shift::query()->create([
            'code' => 'PAGI',
            'name' => 'Shift Pagi',
            'start_time' => '08:00',
            'end_time' => '17:00',
            'break_minutes' => 60,
            'is_active' => true,
        ]);

        Shift::query()->create([
            'code' => 'SIANG',
            'name' => 'Shift Siang',
            'start_time' => '14:00',
            'end_time' => '22:00',
            'break_minutes' => 60,
            'is_active' => true,
        ]);

        Shift::query()->create([
            'code' => 'MALAM',
            'name' => 'Shift Malam',
            'start_time' => '22:00',
            'end_time' => '06:00',
            'break_minutes' => 60,
            'is_active' => true,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function seedOrganizationData(): array
    {
        $root = OrganizationalUnit::query()->firstOrCreate(
            ['code' => 'ROOT'],
            ['name' => 'PT Demo Company', 'parent_id' => null, 'sort_order' => 0, 'is_active' => true]
        );

        $units = [
            'direksi' => OrganizationalUnit::query()->firstOrCreate(
                ['code' => 'DIR'],
                ['name' => 'Direksi', 'parent_id' => $root->id, 'sort_order' => 1, 'is_active' => true]
            ),
            'hr' => OrganizationalUnit::query()->firstOrCreate(
                ['code' => 'HR'],
                ['name' => 'HR', 'parent_id' => $root->id, 'sort_order' => 2, 'is_active' => true]
            ),
            'keuangan' => OrganizationalUnit::query()->firstOrCreate(
                ['code' => 'KEU'],
                ['name' => 'Keuangan', 'parent_id' => $root->id, 'sort_order' => 3, 'is_active' => true]
            ),
            'operasional' => OrganizationalUnit::query()->firstOrCreate(
                ['code' => 'OPS'],
                ['name' => 'Operasional', 'parent_id' => $root->id, 'sort_order' => 4, 'is_active' => true]
            ),
            'it' => OrganizationalUnit::query()->firstOrCreate(
                ['code' => 'IT'],
                ['name' => 'IT', 'parent_id' => $root->id, 'sort_order' => 5, 'is_active' => true]
            ),
            'gudang' => OrganizationalUnit::query()->firstOrCreate(
                ['code' => 'GDG'],
                ['name' => 'Gudang', 'parent_id' => null, 'sort_order' => 6, 'is_active' => true]
            ),
        ];

        $units['gudang']->update(['parent_id' => $units['operasional']->id]);

        $positions = [
            'direktur' => Position::query()->firstOrCreate(
                ['code' => 'DIR'],
                ['name' => 'Direktur Utama', 'level' => 1, 'is_active' => true]
            ),
            'manager' => Position::query()->firstOrCreate(
                ['code' => 'MGR'],
                ['name' => 'Manager', 'level' => 2, 'is_active' => true]
            ),
            'supervisor' => Position::query()->firstOrCreate(
                ['code' => 'SPV'],
                ['name' => 'Supervisor', 'level' => 3, 'is_active' => true]
            ),
            'staff' => Position::query()->firstOrCreate(
                ['code' => 'STF'],
                ['name' => 'Staff', 'level' => 4, 'is_active' => true]
            ),
        ];

        return compact('units', 'positions');
    }

    /**
     * @return array<string, Branch>
     */
    protected function seedBranches(): array
    {
        $jakarta = Branch::query()->firstOrCreate(
            ['code' => 'JKT'],
            [
                'name' => 'Kantor Pusat Jakarta',
                'address' => 'Jl. Sudirman No. 1',
                'city' => 'Jakarta',
                'phone' => '021-1234567',
                'is_active' => true,
                'is_head_office' => true,
            ]
        );

        $jakarta->update(['is_head_office' => true]);
        Branch::query()->where('id', '!=', $jakarta->id)->update(['is_head_office' => false]);

        $surabaya = Branch::query()->firstOrCreate(
            ['code' => 'SBY'],
            [
                'name' => 'Cabang Surabaya',
                'address' => 'Jl. Pemuda No. 10',
                'city' => 'Surabaya',
                'phone' => '031-7654321',
                'is_active' => true,
                'is_head_office' => false,
            ]
        );

        return [
            'jakarta' => $jakarta,
            'surabaya' => $surabaya,
        ];
    }

    protected function seedEmployees(User $adminUser, array $orgData, array $branches): void
    {
        $units = $orgData['units'];
        $positions = $orgData['positions'];
        $jakarta = $branches['jakarta'];
        $surabaya = $branches['surabaya'];

        $shiftPagi = Shift::query()->where('code', 'PAGI')->first();
        $shiftSiang = Shift::query()->where('code', 'SIANG')->first();
        $shiftMalam = Shift::query()->where('code', 'MALAM')->first();

        if (! $shiftPagi) {
            return;
        }

        $hrdUser = User::query()->firstOrCreate(
            ['email' => 'hrd@demo.test'],
            [
                'name' => 'Siti Nurhaliza',
                'username' => 'hrd',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'hr',
            ]
        );
        $hrdUser->update(['role' => 'hr']);

        TenantUser::query()->updateOrCreate(
            ['tenant_id' => self::DEMO_TENANT_ID, 'email' => $hrdUser->email],
            ['username' => $hrdUser->username]
        );

        $adminEmployee = Employee::query()->updateOrCreate(
            ['employee_code' => 'ADM001'],
            [
                'name' => 'Admin HRD',
                'email' => $adminUser->email,
                'phone' => null,
                'position_id' => $positions['direktur']->id,
                'organizational_unit_id' => $units['direksi']->id,
                'branch_id' => $jakarta->id,
                'manager_id' => null,
                'shift_id' => $shiftPagi->id,
                'join_date' => now()->subYears(5),
                'status' => 'active',
                'user_id' => $adminUser->id,
            ]
        );

        $this->ensureSalary($adminEmployee, 15000000, 2000000);

        $hrdEmployee = Employee::query()->updateOrCreate(
            ['employee_code' => 'HRD001'],
            [
                'name' => 'Siti Nurhaliza',
                'email' => $hrdUser->email,
                'phone' => null,
                'position_id' => $positions['manager']->id,
                'organizational_unit_id' => $units['hr']->id,
                'branch_id' => $jakarta->id,
                'manager_id' => $adminEmployee->id,
                'shift_id' => $shiftPagi->id,
                'join_date' => now()->subYears(3),
                'status' => 'active',
                'user_id' => $hrdUser->id,
            ]
        );

        $this->ensureSalary($hrdEmployee, 9000000, 1000000);

        $employees = [
            ['employee_code' => 'EMP001', 'name' => 'Budi Santoso', 'unit' => 'hr', 'position' => 'staff', 'manager' => 'HRD001', 'branch' => 'jakarta', 'shift_id' => $shiftPagi->id, 'basic' => 5500000, 'allowance' => 500000],
            ['employee_code' => 'EMP002', 'name' => 'Siti Rahayu', 'unit' => 'keuangan', 'position' => 'staff', 'manager' => 'ADM001', 'branch' => 'jakarta', 'shift_id' => $shiftPagi->id, 'basic' => 6000000, 'allowance' => 750000],
            ['employee_code' => 'EMP003', 'name' => 'Andi Wijaya', 'unit' => 'operasional', 'position' => 'supervisor', 'manager' => 'ADM001', 'branch' => 'jakarta', 'shift_id' => $shiftSiang->id, 'basic' => 7000000, 'allowance' => 1000000],
            ['employee_code' => 'EMP004', 'name' => 'Dewi Lestari', 'unit' => 'it', 'position' => 'staff', 'manager' => 'ADM001', 'branch' => 'jakarta', 'shift_id' => $shiftPagi->id, 'basic' => 8000000, 'allowance' => 1500000],
            ['employee_code' => 'EMP005', 'name' => 'Rizki Pratama', 'unit' => 'gudang', 'position' => 'staff', 'manager' => 'EMP003', 'branch' => 'surabaya', 'shift_id' => $shiftMalam->id, 'basic' => 4800000, 'allowance' => 400000],
        ];

        $employeeMap = [
            'ADM001' => $adminEmployee,
            'HRD001' => $hrdEmployee,
        ];

        foreach ($employees as $data) {
            $manager = $employeeMap[$data['manager']] ?? null;

            $employee = Employee::query()->updateOrCreate(
                ['employee_code' => $data['employee_code']],
                [
                    'name' => $data['name'],
                    'email' => strtolower(str_replace(' ', '.', $data['name'])).'@demo.test',
                    'phone' => null,
                    'position_id' => $positions[$data['position']]->id,
                    'organizational_unit_id' => $units[$data['unit']]->id,
                    'branch_id' => $branches[$data['branch']]->id,
                    'manager_id' => $manager?->id,
                    'shift_id' => $data['shift_id'],
                    'join_date' => now()->subMonths(rand(6, 36)),
                    'status' => 'active',
                ]
            );

            $employeeMap[$data['employee_code']] = $employee;

            $this->ensureSalary($employee, $data['basic'], $data['allowance']);

            if (! $employee->user_id) {
                app(EmployeeAccountService::class)->createAutoForEmployee(
                    $employee,
                    sendNotification: false,
                    role: 'employee',
                );
            }
        }
    }

    protected function ensureSalary(Employee $employee, int $basic, int $allowance): void
    {
        if ($employee->salaries()->exists()) {
            return;
        }

        EmployeeSalary::query()->create([
            'employee_id' => $employee->id,
            'effective_date' => now()->startOfYear(),
            'basic_salary' => $basic,
            'fixed_allowance' => $allowance,
            'is_active' => true,
            'notes' => 'Gaji awal seed demo',
        ]);
    }

    protected function seedOperationalData(array $branches): void
    {
        $jakarta = $branches['jakarta'];
        $surabaya = $branches['surabaya'];

        WorkLocation::query()->updateOrCreate(
            ['name' => 'Kantor Pusat Demo'],
            [
                'branch_id' => $jakarta->id,
                'latitude' => -6.2000000,
                'longitude' => 106.8166667,
                'radius_meters' => 200,
                'is_active' => true,
                'is_default' => true,
            ]
        );

        WorkLocation::query()->updateOrCreate(
            ['name' => 'Kantor Surabaya Demo'],
            [
                'branch_id' => $surabaya->id,
                'latitude' => -7.2574719,
                'longitude' => 112.7520883,
                'radius_meters' => 200,
                'is_active' => true,
                'is_default' => true,
            ]
        );
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

    protected function seedLeaveData(): void
    {
        $types = [
            ['code' => 'TAHUNAN', 'name' => 'Cuti Tahunan', 'default_quota_days' => 12],
            ['code' => 'SAKIT', 'name' => 'Cuti Sakit', 'default_quota_days' => 12],
            ['code' => 'IZIN', 'name' => 'Izin', 'default_quota_days' => 6, 'is_paid' => false],
        ];

        foreach ($types as $data) {
            LeaveType::query()->updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'default_quota_days' => $data['default_quota_days'],
                    'is_paid' => $data['is_paid'] ?? true,
                    'is_active' => true,
                ]
            );
        }

        $year = (int) now()->year;
        $balanceService = app(EmployeeLeaveBalanceService::class);

        Employee::query()->active()->each(function (Employee $employee) use ($balanceService, $year) {
            $balanceService->ensureBalancesForYear($employee, $year);
        });

        $employee = Employee::query()->where('employee_code', 'EMP001')->first();
        $leaveType = LeaveType::query()->where('code', 'TAHUNAN')->first();

        if ($employee && $leaveType) {
            LeaveRequest::query()->firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveType->id,
                    'start_date' => now()->addWeeks(2)->startOfWeek()->toDateString(),
                    'status' => LeaveRequest::STATUS_PENDING,
                ],
                [
                    'end_date' => now()->addWeeks(2)->startOfWeek()->addDays(2)->toDateString(),
                    'total_days' => 3,
                    'reason' => 'Cuti keluarga (demo pending approval)',
                ]
            );
        }
    }

    protected function seedDeductionAndLoanData(): void
    {
        $types = [
            ['code' => 'BPJS', 'name' => 'BPJS Kesehatan & Ketenagakerjaan'],
            ['code' => 'KOPERASI', 'name' => 'Iuran Koperasi'],
            ['code' => 'PPH21', 'name' => 'PPh 21'],
        ];

        foreach ($types as $data) {
            DeductionType::query()->updateOrCreate(
                ['code' => $data['code']],
                ['name' => $data['name'], 'is_active' => true]
            );
        }

        $bpjs = DeductionType::query()->where('code', 'BPJS')->first();
        $koperasi = DeductionType::query()->where('code', 'KOPERASI')->first();

        $deductionSeeds = [
            ['employee_code' => 'EMP001', 'type' => $bpjs, 'amount' => 150000],
            ['employee_code' => 'EMP002', 'type' => $bpjs, 'amount' => 150000],
            ['employee_code' => 'EMP002', 'type' => $koperasi, 'amount' => 50000],
            ['employee_code' => 'EMP003', 'type' => $koperasi, 'amount' => 75000],
        ];

        foreach ($deductionSeeds as $seed) {
            if (! $seed['type']) {
                continue;
            }

            $employee = Employee::query()->where('employee_code', $seed['employee_code'])->first();

            if (! $employee) {
                continue;
            }

            EmployeeDeduction::query()->firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'deduction_type_id' => $seed['type']->id,
                    'is_active' => true,
                ],
                [
                    'amount' => $seed['amount'],
                    'effective_date' => now()->startOfYear()->toDateString(),
                    'notes' => 'Seed demo pemotongan',
                ]
            );
        }

        $employee = Employee::query()->where('employee_code', 'EMP001')->first();
        $adminUser = User::query()->where('email', 'admin@hrd.test')->first();

        if (! $employee || EmployeeLoan::query()->where('employee_id', $employee->id)->exists()) {
            return;
        }

        $loan = app(EmployeeLoanService::class)->createLoan(
            $employee,
            now()->subMonth()->toDateString(),
            3000000,
            500000,
            'Kasbon demo EMP001',
            $adminUser
        );

        $firstInstallment = $loan->installments()->orderBy('installment_number')->first();

        if ($firstInstallment && $adminUser) {
            app(EmployeeLoanService::class)->payInstallment($firstInstallment, $adminUser, 'Cicilan demo sudah dibayar');
        }
    }

    protected function dropTenantDatabaseIfExists(string $tenantId): void
    {
        $database = config('tenancy.database.prefix').$tenantId;

        DB::connection('central')->statement(
            'DROP DATABASE IF EXISTS `'.str_replace('`', '``', $database).'`'
        );
    }
}
