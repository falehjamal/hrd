<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function __construct(
        private readonly WhatsAppGatewayService $gateway,
    ) {}

    public function send(string $phone, string $message): bool
    {
        if (! tenant_whatsapp_is_configured()) {
            Log::info('WhatsApp tidak dikonfigurasi, pesan dilewati.', ['phone' => $phone]);

            return false;
        }

        $sent = $this->gateway->send($phone, $message);

        if (! $sent) {
            Log::warning('Gagal mengirim pesan WhatsApp.', [
                'phone' => $phone,
                'instance_key' => $this->gateway->instanceKey(),
            ]);
        }

        return $sent;
    }
}
