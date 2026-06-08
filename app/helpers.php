<?php

use App\Models\Attendance;
use App\Models\OvertimeRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;

if (! function_exists('tenant_app_name')) {
    function tenant_app_name(): string
    {
        if (tenancy()->initialized && tenant()) {
            return tenant()->displayName();
        }

        return (string) config('platform.name', config('app.name'));
    }
}

if (! function_exists('tenant_sidebar_title')) {
    function tenant_sidebar_title(): string
    {
        return mb_strtoupper(tenant_app_name(), 'UTF-8');
    }
}

if (! function_exists('platform_sidebar_title')) {
    function platform_sidebar_title(): string
    {
        return mb_strtoupper((string) config('platform.name', config('app.name')), 'UTF-8');
    }
}

if (! function_exists('sidebar_brand_initial')) {
    function sidebar_brand_initial(?string $name = null): string
    {
        $name = $name ?? tenant_app_name();

        return mb_strtoupper(mb_substr(trim($name), 0, 1, 'UTF-8'), 'UTF-8');
    }
}

if (! function_exists('format_rupiah')) {
    function format_rupiah(float|int|string|null $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('attendance_status_label')) {
    function attendance_status_label(string $status): string
    {
        return Attendance::statusLabels()[$status] ?? $status;
    }
}

if (! function_exists('overtime_status_label')) {
    function overtime_status_label(string $status): string
    {
        return OvertimeRequest::statusLabels()[$status] ?? $status;
    }
}

if (! function_exists('tenant_setting')) {
    function tenant_setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('tenant_mail_is_configured')) {
    function tenant_mail_is_configured(): bool
    {
        if (! Setting::isTruthy('mail_enabled')) {
            return false;
        }

        return filled(tenant_setting('mail_username'))
            && filled(tenant_setting('mail_password'));
    }
}

if (! function_exists('tenant_whatsapp_is_configured')) {
    function tenant_whatsapp_is_configured(): bool
    {
        if (! Setting::isTruthy('wa_enabled')) {
            return false;
        }

        return filled(tenant_setting('wa_base_url'))
            && filled(tenant_setting('wa_token'));
    }
}

if (! function_exists('apply_tenant_mail_config')) {
    function apply_tenant_mail_config(): bool
    {
        if (! tenant_mail_is_configured()) {
            return false;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', tenant_setting('mail_host', 'smtp.gmail.com'));
        Config::set('mail.mailers.smtp.port', (int) tenant_setting('mail_port', 587));
        Config::set('mail.mailers.smtp.encryption', tenant_setting('mail_encryption', 'tls'));
        Config::set('mail.mailers.smtp.username', tenant_setting('mail_username'));
        Config::set('mail.mailers.smtp.password', tenant_setting('mail_password'));
        Config::set('mail.from.address', tenant_setting('mail_from_address', tenant_setting('mail_username')));
        Config::set('mail.from.name', tenant_setting('mail_from_name', tenant_app_name()));

        return true;
    }
}
