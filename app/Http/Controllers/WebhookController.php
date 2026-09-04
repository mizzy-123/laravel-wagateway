<?php

namespace App\Http\Controllers;

use App\Models\WaDevice;
use App\Models\WaMessage;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

#[Group('Webhooks', weight: 90)]
class WebhookController extends Controller
{
    /**
     * Terima event dari WPPConnect Server.
     */
    #[Endpoint(
        operationId: 'whatsappWebhook',
        title: 'WhatsApp webhook',
        description: 'Endpoint callback untuk event session dan pesan dari WPPConnect. Dilindungi header/secret `WA_WEBHOOK_SECRET`.',
    )]
    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->isAuthorized($request)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? null;
        $session = $payload['session'] ?? null;

        Log::info('WA webhook received', [
            'event' => $event,
            'session' => $session,
        ]);

        if (! is_string($session) || $session === '') {
            return response()->json(['message' => 'Session tidak ditemukan.'], 422);
        }

        $device = WaDevice::query()->where('session', $session)->first();

        if ($device === null) {
            Log::warning('WA webhook for unknown session', ['session' => $session]);

            return response()->json(['message' => 'Device tidak ditemukan.'], 404);
        }

        match ($event) {
            'qrcode' => $this->handleQrCode($device, $payload),
            'phoneCode' => $this->handlePhoneCode($device, $payload),
            'status-find' => $this->handleStatusFind($device, $payload),
            'closesession', 'logoutsession' => $this->handleSessionClosed($device, $payload),
            'onmessage', 'onselfmessage', 'unreadmessages' => $this->handleIncomingMessage($device, $payload),
            'onack' => $this->handleAck($device, $payload),
            default => null,
        };

        $device->update(['last_seen_at' => now()]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handleQrCode(WaDevice $device, array $payload): void
    {
        $updates = [
            'status' => 'connecting',
            'session_status' => 'QRCODE',
        ];

        if (isset($payload['qrcode']) && is_string($payload['qrcode'])) {
            $qrcode = $payload['qrcode'];
            if (! str_starts_with($qrcode, 'data:image')) {
                $qrcode = 'data:image/png;base64,'.$qrcode;
            }

            $updates['qrcode'] = $qrcode;
        }

        $device->update($updates);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handlePhoneCode(WaDevice $device, array $payload): void
    {
        $device->update([
            'status' => 'connecting',
            'session_status' => 'PHONE_CODE',
            'qrcode' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handleStatusFind(WaDevice $device, array $payload): void
    {
        $status = is_string($payload['status'] ?? null) ? $payload['status'] : null;

        $updates = [
            'session_status' => $status,
            'last_seen_at' => now(),
        ];

        if (in_array($status, ['isLogged', 'inChat', 'CONNECTED', 'connected'], true)) {
            $updates['status'] = 'connected';
            $updates['connected_at'] = $device->connected_at ?? now();
            $updates['qrcode'] = null;
        } elseif (in_array($status, ['qrReadSuccess', 'notLogged', 'QRCODE'], true)) {
            $updates['status'] = 'connecting';
        } elseif (in_array($status, ['browserClose', 'disconnected'], true)) {
            $updates['status'] = 'disconnected';
            $updates['qrcode'] = null;
        }

        if (isset($payload['phone']) && is_string($payload['phone']) && $payload['phone'] !== '') {
            $updates['phone'] = $payload['phone'];
        }

        $device->update($updates);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handleSessionClosed(WaDevice $device, array $payload): void
    {
        $device->update([
            'status' => 'disconnected',
            'qrcode' => null,
            'session_status' => $payload['event'] ?? 'closed',
            'connected_at' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handleIncomingMessage(WaDevice $device, array $payload): void
    {
        if (($payload['event'] ?? null) === 'unreadmessages' && isset($payload['messages']) && is_array($payload['messages'])) {
            foreach ($payload['messages'] as $message) {
                if (is_array($message)) {
                    $this->storeMessage($device, $message, 'inbound');
                }
            }

            return;
        }

        $this->storeMessage($device, $payload, ($payload['fromMe'] ?? false) ? 'outbound' : 'inbound');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handleAck(WaDevice $device, array $payload): void
    {
        $messageId = $payload['id'] ?? null;

        if (! is_string($messageId) || $messageId === '') {
            return;
        }

        WaMessage::query()
            ->where('wa_device_id', $device->id)
            ->where('wa_message_id', $messageId)
            ->update([
                'status' => $this->mapAckStatus($payload['ack'] ?? null),
            ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function storeMessage(WaDevice $device, array $payload, string $direction): void
    {
        $messageId = $payload['id'] ?? null;

        if (is_string($messageId) && $messageId !== '') {
            $exists = WaMessage::query()
                ->where('wa_device_id', $device->id)
                ->where('wa_message_id', $messageId)
                ->exists();

            if ($exists) {
                return;
            }
        }

        WaMessage::query()->create([
            'wa_device_id' => $device->id,
            'wa_message_id' => is_string($messageId) ? $messageId : null,
            'direction' => $direction,
            'from_number' => $payload['from'] ?? null,
            'to_number' => $payload['to'] ?? null,
            'body' => $payload['body'] ?? $payload['caption'] ?? null,
            'type' => $payload['type'] ?? 'chat',
            'status' => $direction === 'inbound' ? 'received' : 'sent',
            'notify_name' => $payload['notifyName'] ?? null,
            'is_group' => (bool) ($payload['isGroupMsg'] ?? false),
            'raw_payload' => $payload,
        ]);
    }

    private function mapAckStatus(mixed $ack): string
    {
        return match ((int) $ack) {
            1 => 'sent',
            2 => 'delivered',
            3 => 'read',
            default => 'pending',
        };
    }

    private function isAuthorized(Request $request): bool
    {
        $secret = config('whatsapp.webhook_secret');

        if (! is_string($secret) || $secret === '') {
            return true;
        }

        return hash_equals($secret, (string) $request->header('X-Webhook-Secret', ''));
    }
}
