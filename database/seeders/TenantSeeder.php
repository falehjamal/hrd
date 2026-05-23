<?php

namespace Database\Seeders;

use App\Models\Central\TenantUser;
use App\Models\Tenant;
use App\Models\User;
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

        if (! $tenant) {
            // migrate:fresh menghapus record di central, tapi DB tenant lama masih ada.
            $this->dropTenantDatabaseIfExists(self::DEMO_TENANT_ID);

            $tenant = Tenant::query()->create([
                'id' => self::DEMO_TENANT_ID,
                'name' => 'Demo Company',
                'slug' => 'demo',
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

    protected function dropTenantDatabaseIfExists(string $tenantId): void
    {
        $database = config('tenancy.database.prefix').$tenantId;

        DB::connection('central')->statement(
            'DROP DATABASE IF EXISTS `'.str_replace('`', '``', $database).'`'
        );
    }
}
