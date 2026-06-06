<?php

namespace App\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    public function __construct(
        private readonly WhatsAppService $whatsAppService,
    ) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsapp')) {
            return;
        }

        /** @var array{phone: string, message: string} $data */
        $data = $notification->toWhatsapp($notifiable);

        $this->whatsAppService->send($data['phone'], $data['message']);
    }
}
