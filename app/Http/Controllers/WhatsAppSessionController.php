<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\WhatsAppGatewayService;
use Illuminate\Http\JsonResponse;

class WhatsAppSessionController extends Controller
{
    public function __construct(
        private readonly WhatsAppGatewayService $gateway,
    ) {}

    public function connect(): JsonResponse
    {
        abort_if(auth()->user()->employee, 403);

        $result = $this->gateway->connect();
        $this->syncSender($result);

        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

    public function status(): JsonResponse
    {
        abort_if(auth()->user()->employee, 403);

        $result = $this->gateway->status();
        $this->syncSender($result);

        return response()->json($result);
    }

    public function disconnect(): JsonResponse
    {
        abort_if(auth()->user()->employee, 403);

        $result = $this->gateway->disconnect();

        if ($result['success'] ?? false) {
            Setting::set('wa_sender', null);
            Setting::flushCache();
        }

        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

    /**
     * @param  array{success?: bool, data?: array<string, mixed>|null}  $result
     */
    private function syncSender(array $result): void
    {
        $data = $result['data'] ?? null;

        if (! is_array($data) || ($data['state'] ?? null) !== 'connected') {
            return;
        }

        $phone = $data['phone'] ?? null;

        if (! filled($phone)) {
            return;
        }

        Setting::set('wa_sender', $phone);
        Setting::flushCache();
    }
}
