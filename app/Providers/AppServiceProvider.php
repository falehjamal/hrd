<?php

namespace App\Providers;

use App\Auth\TenantAwareUserProvider;
use App\Channels\WhatsAppChannel;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        User::observe(UserObserver::class);

        Auth::provider('tenant-aware-eloquent', function ($app, array $config) {
            return new TenantAwareUserProvider(
                $app['hash'],
                $config['model']
            );
        });

        Notification::extend('whatsapp', function ($app) {
            return $app->make(WhatsAppChannel::class);
        });
    }
}
