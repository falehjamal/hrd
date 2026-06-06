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
        'wa_provider' => null,
        'wa_base_url' => null,
        'wa_token' => null,
        'wa_sender' => null,
    ];

    public function edit(): View
    {
        abort_if(auth()->user()->employee, 403);

        $settings = Setting::getMany(array_keys(self::DEFAULTS), self::DEFAULTS);

        return view('settings.edit', compact('settings'));
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
            'wa_provider' => $validated['wa_provider'] ?? null,
            'wa_base_url' => $validated['wa_base_url'] ?? null,
            'wa_token' => $validated['wa_token'] ?? null,
            'wa_sender' => $validated['wa_sender'] ?? null,
        ];

        if (filled($validated['mail_password'] ?? null)) {
            $data['mail_password'] = $validated['mail_password'];
        } else {
            $data['mail_password'] = Setting::get('mail_password');
        }

        if (filled($validated['wa_token'] ?? null)) {
            $data['wa_token'] = $validated['wa_token'];
        } else {
            $data['wa_token'] = Setting::get('wa_token');
        }

        Setting::setMany($data);
        Setting::flushCache();

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
