<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWaDeviceRequest;
use App\Models\WaDevice;
use App\Services\WppConnectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class WaDeviceController extends Controller
{
    public function __construct(private WppConnectService $wppConnect) {}

    public function store(StoreWaDeviceRequest $request): RedirectResponse
    {
        WaDevice::query()->create($request->validated());

        return redirect()
            ->route('dashboard.devices')
            ->with('success', 'Perangkat berhasil ditambahkan.');
    }

    public function connect(WaDevice $device): JsonResponse
    {
        try {
            $tokenResponse = $this->wppConnect->generateToken($device->session);
            $token = $tokenResponse['token'] ?? null;

            if (! is_string($token) || $token === '') {
                throw new RuntimeException('Token session tidak diterima dari WPPConnect.');
            }

            $device->update([
                'status' => 'connecting',
                'token' => $token,
                'qrcode' => null,
                'session_status' => 'STARTING',
            ]);

            $sessionResponse = $this->wppConnect->startSession(
                $device->session,
                $token,
                config('whatsapp.webhook_url'),
            );

            if (isset($sessionResponse['qrcode']) && is_string($sessionResponse['qrcode'])) {
                $qrcode = $sessionResponse['qrcode'];
                if (! str_starts_with($qrcode, 'data:image')) {
                    $qrcode = 'data:image/png;base64,'.$qrcode;
                }

                $device->update(['qrcode' => $qrcode]);
            }

            if (isset($sessionResponse['status']) && in_array($sessionResponse['status'], ['isLogged', 'inChat', 'CONNECTED'], true)) {
                $device->update([
                    'status' => 'connected',
                    'connected_at' => now(),
                    'qrcode' => null,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Session dimulai. Silakan scan QR code.',
                'device' => $this->devicePayload($device->fresh()),
            ]);
        } catch (RuntimeException $exception) {
            $device->update([
                'status' => 'disconnected',
                'session_status' => 'ERROR',
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function status(WaDevice $device): JsonResponse
    {
        if ($device->token && $device->status !== 'connected') {
            try {
                $connection = $this->wppConnect->checkConnection($device->session, $device->token);

                if (($connection['status'] ?? false) === true || ($connection['message'] ?? '') === 'Connected') {
                    $device->update([
                        'status' => 'connected',
                        'connected_at' => $device->connected_at ?? now(),
                        'qrcode' => null,
                        'session_status' => 'CONNECTED',
                    ]);
                }
            } catch (RuntimeException) {
                // Status tetap mengandalkan webhook jika API check gagal.
            }
        }

        return response()->json([
            'device' => $this->devicePayload($device->fresh()),
        ]);
    }

    public function disconnect(WaDevice $device): JsonResponse
    {
        try {
            if ($device->token) {
                $this->wppConnect->closeSession($device->session, $device->token);
            }
        } catch (RuntimeException) {
            // Lanjut reset status lokal meski API gagal.
        }

        $device->update([
            'status' => 'disconnected',
            'qrcode' => null,
            'session_status' => 'CLOSED',
            'connected_at' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Perangkat berhasil diputuskan.',
            'device' => $this->devicePayload($device->fresh()),
        ]);
    }

    public function destroy(WaDevice $device): RedirectResponse
    {
        try {
            if ($device->token) {
                $this->wppConnect->logoutSession($device->session, $device->token);
            }
        } catch (RuntimeException) {
            // Tetap hapus record lokal.
        }

        $device->delete();

        return redirect()
            ->route('dashboard.devices')
            ->with('success', 'Perangkat berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function devicePayload(WaDevice $device): array
    {
        return [
            'id' => $device->id,
            'name' => $device->name,
            'session' => $device->session,
            'phone' => $device->phone,
            'status' => $device->status,
            'session_status' => $device->session_status,
            'qrcode' => $device->qrcode,
            'messages_today' => $device->messagesTodayCount(),
            'last_seen' => $device->lastSeenLabel(),
            'connected_at' => $device->connected_at?->toIso8601String(),
        ];
    }
}
