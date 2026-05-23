<?php

namespace App\Providers;

use App\Auth\TenantAwareUserProvider;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        User::observe(UserObserver::class);

        Auth::provider('tenant-aware-eloquent', function ($app, array $config) {
            return new TenantAwareUserProvider(
                $app['hash'],
                $config['model']
            );
        });
    }
}
