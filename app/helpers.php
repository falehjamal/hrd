<?php

if (! function_exists('tenant_app_name')) {
    function tenant_app_name(): string
    {
        if (tenancy()->initialized && tenant()) {
            return tenant()->displayName();
        }

        return (string) config('platform.name', config('app.name'));
    }
}

if (! function_exists('format_rupiah')) {
    function format_rupiah(float|int|string|null $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}
