<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function send(string $phone, string $message): bool
    {
        if (! tenant_whatsapp_is_configured()) {
            Log::info('WhatsApp tidak dikonfigurasi, pesan dilewati.', ['phone' => $phone]);

            return false;
        }

        Log::info('WhatsApp pesan (stub — integrasi menyusul)', [
            'phone' => $phone,
            'message' => $message,
            'provider' => tenant_setting('wa_provider'),
            'base_url' => tenant_setting('wa_base_url'),
            'sender' => tenant_setting('wa_sender'),
        ]);

        return true;
    }
}
