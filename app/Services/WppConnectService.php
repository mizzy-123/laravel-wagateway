<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WppConnectService
{
    public function sendMessage(string $session, string $token, string $phone, string $message, bool $isGroup = false, bool $isNewsletter = false, bool $isLid = false)
    {
        $response = $this->authorizedClient($this->normalizeToken($token))->post("/api/{$session}/send-message", [
            'phone' => $phone,
            'isGroup' => $isGroup,
            'isNewsletter' => $isNewsletter,
            'isLid' => $isLid,
            'message' => $message,
        ]);

        return $this->decode($response, 'Gagal mengirim pesan');
    }

    public function blastMessage(string $session, string $token, array $phones, bool $consentConfirmed, string $message): array
    {
        $response = $this->authorizedClient($this->normalizeToken($token))->post("/api/{$session}/wa-blast", [
            'phones' => array_values($phones),
            'message' => $message,
            'consentConfirmed' => $consentConfirmed,
        ]);

        return $this->decode($response, 'Gagal mengirim pesan blast');
    }

    /**
     * @return array<string, mixed>
     */
    public function getBlastStatus(string $session, string $token, string $jobId): array
    {
        $response = $this->authorizedClient($this->normalizeToken($token))
            ->get("/api/{$session}/wa-blast/{$jobId}");

        return $this->decode($response, 'Gagal mengambil status blast.');
    }

    /**
     * @return array<string, mixed>
     */
    public function getBlastFailed(string $session, string $token, string $jobId): array
    {
        $response = $this->authorizedClient($this->normalizeToken($token))
            ->get("/api/{$session}/wa-blast/{$jobId}/failed");

        return $this->decode($response, 'Gagal mengambil daftar pesan gagal.');
    }

    /**
     * @param  list<int>|null  $indexes
     * @return array<string, mixed>
     */
    public function retryBlastFailed(string $session, string $token, string $jobId, ?array $indexes = null): array
    {
        $payload = [];

        if ($indexes !== null && $indexes !== []) {
            $payload['indexes'] = array_values(array_map('intval', $indexes));
        }

        $response = $this->authorizedClient($this->normalizeToken($token))
            ->post("/api/{$session}/wa-blast/{$jobId}/retry-failed", $payload);

        return $this->decode($response, 'Gagal retry pesan blast yang gagal.');
    }

    public function generateToken(string $session): array
    {
        $response = $this->client()
            ->post("/api/{$session}/{$this->secretKey()}/generate-token");

        return $this->decode($response, 'Gagal membuat token session.');
    }

    public function startSession(string $session, string $token, string $webhookUrl): array
    {
        $response = $this->authorizedClient($this->normalizeToken($token))
            ->post("/api/{$session}/start-session", [
                'webhook' => $webhookUrl,
                'waitQrCode' => true,
            ]);

        return $this->decode($response, 'Gagal memulai session WhatsApp.');
    }

    public function checkConnection(string $session, string $token): array
    {
        $response = $this->authorizedClient($this->normalizeToken($token))
            ->get("/api/{$session}/check-connection-session");

        return $this->decode($response, 'Gagal memeriksa koneksi session.');
    }

    public function closeSession(string $session, string $token): array
    {
        $response = $this->authorizedClient($this->normalizeToken($token))
            ->post("/api/{$session}/close-session");

        return $this->decode($response, 'Gagal menutup session WhatsApp.');
    }

    public function logoutSession(string $session, string $token): array
    {
        $response = $this->authorizedClient($this->normalizeToken($token))
            ->post("/api/{$session}/logout-session");

        return $this->decode($response, 'Gagal logout session WhatsApp.');
    }

    /**
     * WPPConnect expects Bearer {token} only — not session:token.
     */
    public function normalizeToken(string $token): string
    {
        if (str_contains($token, ':')) {
            return explode(':', $token, 2)[1] ?? $token;
        }

        return $token;
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(config('whatsapp.base_url'))
            ->connectTimeout(config('whatsapp.http.connect_timeout'))
            ->timeout(config('whatsapp.http.timeout'))
            ->acceptJson();
    }

    private function authorizedClient(string $token): PendingRequest
    {
        return $this->client()->withToken($token);
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(Response $response, string $fallbackMessage): array
    {
        // 2xx termasuk 202 Accepted (wa-blast queued) dianggap sukses.
        if ($response->failed()) {
            $message = $response->json('message')
                ?? $response->json('error')
                ?? $fallbackMessage;

            throw new RuntimeException(is_string($message) ? $message : $fallbackMessage);
        }

        return $response->json() ?? [];
    }

    private function secretKey(): string
    {
        $secretKey = config('whatsapp.secret_key');

        if (! is_string($secretKey) || $secretKey === '') {
            throw new RuntimeException('WA_SECRET_KEY belum dikonfigurasi.');
        }

        return $secretKey;
    }
}
