<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /** @var array<string, mixed> */
    private const DEFAULTS = [
        'mail_enabled' => '0',
        'mail_host' => 'smtp.gmail.com',
        'mail_port' => '587',
        'mail_encryption' => 'tls',
        'mail_username' => null,
        'mail_password' => null,
        'mail_from_address' => null,
        'mail_from_name' => null,
        'wa_enabled' => '0',
        'wa_sender' => null,
    ];

    public function edit(): View
    {
        abort_unless(auth()->user()->isHrUser(), 403);

        $settings = Setting::getMany(array_keys(self::DEFAULTS), self::DEFAULTS);
        $waGatewayConfigured = filled(config('services.whatsapp_gateway.url'))
            && filled(config('services.whatsapp_gateway.key'));

        return view('settings.edit', compact('settings', 'waGatewayConfigured'));
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'mail_enabled' => $request->boolean('mail_enabled') ? '1' : '0',
            'mail_host' => $validated['mail_host'] ?? self::DEFAULTS['mail_host'],
            'mail_port' => (string) ($validated['mail_port'] ?? self::DEFAULTS['mail_port']),
            'mail_encryption' => $validated['mail_encryption'] ?? self::DEFAULTS['mail_encryption'],
            'mail_username' => $validated['mail_username'] ?? null,
            'mail_from_address' => $validated['mail_from_address'] ?? null,
            'mail_from_name' => $validated['mail_from_name'] ?? null,
            'wa_enabled' => $request->boolean('wa_enabled') ? '1' : '0',
        ];

        if (filled($validated['mail_password'] ?? null)) {
            $data['mail_password'] = $validated['mail_password'];
        } else {
            $data['mail_password'] = Setting::get('mail_password');
        }

        Setting::setMany($data);
        Setting::flushCache();

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
