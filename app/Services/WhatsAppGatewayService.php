<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppGatewayService
{
    public function instanceKey(): string
    {
        return 'hrd_'.tenant()->getTenantKey();
    }

    public function isConfigured(): bool
    {
        return filled(config('services.whatsapp_gateway.url'))
            && filled(config('services.whatsapp_gateway.key'));
    }

    /**
     * @return array{success: bool, message: string, data: array<string, mixed>|null}
     */
    public function connect(): array
    {
        return $this->request('post', '/sessions/start', [
            'instance_key' => $this->instanceKey(),
        ]);
    }

    /**
     * @return array{success: bool, message: string, data: array<string, mixed>|null}
     */
    public function status(): array
    {
        return $this->request('get', '/sessions/status/'.$this->instanceKey());
    }

    /**
     * @return array{success: bool, message: string, data: array<string, mixed>|null}
     */
    public function disconnect(): array
    {
        return $this->request('delete', '/sessions/'.$this->instanceKey());
    }

    public function send(string $phone, string $message): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $result = $this->request('post', '/messages/send-text', [
            'instance_key' => $this->instanceKey(),
            'phone' => $phone,
            'message' => $message,
        ]);

        return $result['success'];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, message: string, data: array<string, mixed>|null}
     */
    protected function request(string $method, string $path, array $payload = []): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Gateway WhatsApp belum dikonfigurasi.',
                'data' => null,
            ];
        }

        try {
            $client = Http::baseUrl(rtrim((string) config('services.whatsapp_gateway.url'), '/'))
                ->withHeaders(['x-api-key' => config('services.whatsapp_gateway.key')])
                ->acceptJson()
                ->timeout(30);

            $response = match ($method) {
                'post' => $client->post($path, $payload),
                'get' => $client->get($path),
                'delete' => $client->delete($path),
                default => throw new \InvalidArgumentException("Metode HTTP tidak didukung: {$method}"),
            };

            /** @var array<string, mixed>|null $body */
            $body = $response->json();

            if (! is_array($body)) {
                return [
                    'success' => false,
                    'message' => 'Respons gateway tidak valid.',
                    'data' => null,
                ];
            }

            /** @var array<string, mixed>|null $data */
            $data = is_array($body['data'] ?? null) ? $body['data'] : null;

            return [
                'success' => (bool) ($body['success'] ?? $response->successful()),
                'message' => (string) ($body['message'] ?? ''),
                'data' => $data,
            ];
        } catch (ConnectionException $exception) {
            Log::warning('WhatsApp gateway tidak terjangkau.', [
                'error' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gateway WhatsApp tidak terjangkau. Pastikan server WA berjalan.',
                'data' => null,
            ];
        }
    }
}
