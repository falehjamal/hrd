<?php

namespace Database\Seeders;

use App\Models\Central\PlatformUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformUserSeeder extends Seeder
{
    public function run(): void
    {
        PlatformUser::query()->firstOrCreate(
            ['email' => 'platform@hrd.test'],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
