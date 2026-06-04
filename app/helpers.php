<?php

use App\Models\Attendance;
use App\Models\OvertimeRequest;

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
