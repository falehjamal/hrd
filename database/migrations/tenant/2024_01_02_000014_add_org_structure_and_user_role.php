<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['hr', 'employee'])->default('employee')->after('password');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable()->after('phone')->constrained('positions')->nullOnDelete();
            $table->foreignId('organizational_unit_id')->nullable()->after('position_id')->constrained('organizational_units')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->after('organizational_unit_id')->constrained('employees')->nullOnDelete();
        });

        $this->migrateLegacyEmployeeData();

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['department', 'position']);
        });

        $this->migrateUserRoles();
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('department')->nullable()->after('phone');
            $table->string('position')->nullable()->after('department');
        });

        $employees = DB::table('employees')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->leftJoin('organizational_units', 'employees.organizational_unit_id', '=', 'organizational_units.id')
            ->select('employees.id', 'positions.name as position_name', 'organizational_units.name as unit_name')
            ->get();

        foreach ($employees as $employee) {
            DB::table('employees')->where('id', $employee->id)->update([
                'department' => $employee->unit_name,
                'position' => $employee->position_name,
            ]);
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropForeign(['organizational_unit_id']);
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['position_id', 'organizational_unit_id', 'manager_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    protected function migrateLegacyEmployeeData(): void
    {
        if (! Schema::hasColumn('employees', 'department')) {
            return;
        }

        $departments = DB::table('employees')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department');

        $unitMap = [];
        foreach ($departments as $department) {
            $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $department), 0, 10)) ?: 'UNIT';
            $uniqueCode = $code;
            $suffix = 1;
            while (DB::table('organizational_units')->where('code', $uniqueCode)->exists()) {
                $uniqueCode = $code.$suffix;
                $suffix++;
            }

            $id = DB::table('organizational_units')->insertGetId([
                'code' => $uniqueCode,
                'name' => $department,
                'parent_id' => null,
                'sort_order' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $unitMap[$department] = $id;
        }

        $positions = DB::table('employees')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->pluck('position');

        $positionMap = [];
        foreach ($positions as $position) {
            $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $position), 0, 10)) ?: 'POS';
            $uniqueCode = $code;
            $suffix = 1;
            while (DB::table('positions')->where('code', $uniqueCode)->exists()) {
                $uniqueCode = $code.$suffix;
                $suffix++;
            }

            $id = DB::table('positions')->insertGetId([
                'code' => $uniqueCode,
                'name' => $position,
                'level' => 1,
                'description' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $positionMap[$position] = $id;
        }

        $employees = DB::table('employees')->get(['id', 'department', 'position']);
        foreach ($employees as $employee) {
            DB::table('employees')->where('id', $employee->id)->update([
                'organizational_unit_id' => $unitMap[$employee->department] ?? null,
                'position_id' => $positionMap[$employee->position] ?? null,
            ]);
        }
    }

    protected function migrateUserRoles(): void
    {
        $hrUserIds = DB::table('users')
            ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
            ->whereNull('employees.id')
            ->pluck('users.id');

        if ($hrUserIds->isNotEmpty()) {
            DB::table('users')->whereIn('id', $hrUserIds)->update(['role' => 'hr']);
        }
    }
};
