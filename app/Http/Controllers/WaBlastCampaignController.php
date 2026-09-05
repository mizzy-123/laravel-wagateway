<?php

namespace App\Http\Controllers;

use App\Models\WaBlastCampaign;
use App\Services\WppConnectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class WaBlastCampaignController extends Controller
{
    public function __construct(private WppConnectService $wppConnect) {}

    public function index(): View
    {
        $campaigns = WaBlastCampaign::query()
            ->with('device:id,name,session,status')
            ->latest()
            ->paginate(15);

        return view('dashboard.blasts.index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function show(WaBlastCampaign $blast): View
    {
        $blast->load('device');

        $failedItems = [];
        $syncError = null;

        try {
            $this->syncProgress($blast);

            if ($blast->device?->token) {
                $failedResponse = $this->wppConnect->getBlastFailed(
                    $blast->device->session,
                    $blast->device->token,
                    $blast->job_id,
                );

                $failedItems = data_get($failedResponse, 'response.failed', []);
                if (! is_array($failedItems)) {
                    $failedItems = [];
                }

                $totalFailed = (int) data_get($failedResponse, 'response.totalFailed', count($failedItems));
                if ($totalFailed !== $blast->failed) {
                    $blast->update(['failed' => $totalFailed]);
                }
            }
        } catch (RuntimeException $e) {
            $syncError = $e->getMessage();
        }

        return view('dashboard.blasts.show', [
            'blast' => $blast->fresh('device'),
            'failedItems' => $failedItems,
            'syncError' => $syncError,
        ]);
    }

    public function refresh(WaBlastCampaign $blast): RedirectResponse
    {
        try {
            $this->syncProgress($blast);

            return redirect()
                ->route('dashboard.blasts.show', $blast)
                ->with('success', 'Status blast berhasil diperbarui.');
        } catch (RuntimeException $e) {
            return redirect()
                ->route('dashboard.blasts.show', $blast)
                ->with('error', 'Gagal memperbarui status: '.$e->getMessage());
        }
    }

    public function retryFailed(Request $request, WaBlastCampaign $blast): RedirectResponse
    {
        $data = $request->validate([
            'indexes' => ['nullable', 'array'],
            'indexes.*' => ['integer', 'min:0'],
        ]);

        $device = $blast->device;

        if ($device === null || ! $device->token) {
            return redirect()
                ->route('dashboard.blasts.show', $blast)
                ->with('error', 'Perangkat tidak tersedia atau token hilang.');
        }

        try {
            $indexes = $data['indexes'] ?? null;

            $this->wppConnect->retryBlastFailed(
                session: $device->session,
                token: $device->token,
                jobId: $blast->job_id,
                indexes: $indexes,
            );

            $this->syncProgress($blast);

            $message = ($indexes === null || $indexes === [])
                ? 'Retry semua pesan gagal berhasil diantrikan.'
                : 'Retry '.count($indexes).' nomor gagal berhasil diantrikan.';

            return redirect()
                ->route('dashboard.blasts.show', $blast)
                ->with('success', $message);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('dashboard.blasts.show', $blast)
                ->with('error', 'Gagal retry: '.$e->getMessage());
        }
    }

    public function failed(WaBlastCampaign $blast): JsonResponse
    {
        $device = $blast->device;

        if ($device === null || ! $device->token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Perangkat tidak tersedia atau token hilang.',
            ], 422);
        }

        try {
            $this->syncProgress($blast);

            $failedResponse = $this->wppConnect->getBlastFailed(
                $device->session,
                $device->token,
                $blast->job_id,
            );

            return response()->json([
                'status' => 'success',
                'campaign' => [
                    'id' => $blast->id,
                    'job_id' => $blast->job_id,
                    'status' => $blast->fresh()->status,
                    'total' => $blast->fresh()->total,
                    'queued' => $blast->fresh()->queued,
                    'sent' => $blast->fresh()->sent,
                    'failed' => $blast->fresh()->failed,
                    'cancelled' => $blast->fresh()->cancelled,
                ],
                'failed' => data_get($failedResponse, 'response.failed', []),
                'total_failed' => data_get($failedResponse, 'response.totalFailed', 0),
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    private function syncProgress(WaBlastCampaign $blast): void
    {
        $device = $blast->device;

        if ($device === null || ! $device->token) {
            throw new RuntimeException('Perangkat tidak tersedia atau token hilang.');
        }

        $statusResponse = $this->wppConnect->getBlastStatus(
            $device->session,
            $device->token,
            $blast->job_id,
        );

        $blast->syncFromRemote($statusResponse);
    }
}
