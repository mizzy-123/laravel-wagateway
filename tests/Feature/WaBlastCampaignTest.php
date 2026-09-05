<?php

use App\Models\User;
use App\Models\WaBlastCampaign;
use App\Models\WaDevice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('stores blast job id and redirects to campaign detail', function () {
    $user = User::factory()->create();
    $device = WaDevice::query()->create([
        'name' => 'Front Office',
        'session' => 'FRONT_OFFICE',
        'status' => 'connected',
        'token' => 'test-token',
    ]);

    $jobId = 'a1b2c3d4-e5f6-7890-abcd-ef1234567890';

    Http::fake([
        '*/api/FRONT_OFFICE/wa-blast' => Http::response([
            'status' => 'success',
            'response' => [
                'id' => $jobId,
                'session' => 'FRONT_OFFICE',
                'status' => 'queued',
                'total' => 2,
                'queued' => 2,
                'sent' => 0,
                'failed' => 0,
                'cancelled' => 0,
            ],
        ], 202),
    ]);

    $response = $this->actingAs($user)->post(route('dashboard.send.blast'), [
        'device_id' => $device->id,
        'phones' => "628111111111\n628222222222",
        'message' => 'Halo dari RS Roemani',
        'consent_confirmed' => '1',
    ]);

    $campaign = WaBlastCampaign::query()->first();

    expect($campaign)->not->toBeNull()
        ->and($campaign->job_id)->toBe($jobId)
        ->and($campaign->total)->toBe(2)
        ->and($campaign->status)->toBe('queued');

    $response->assertRedirect(route('dashboard.blasts.show', $campaign));
});

it('shows failed blast recipients and retries selected indexes', function () {
    $user = User::factory()->create();
    $device = WaDevice::query()->create([
        'name' => 'Front Office',
        'session' => 'FRONT_OFFICE',
        'status' => 'connected',
        'token' => 'test-token',
    ]);

    $campaign = WaBlastCampaign::query()->create([
        'wa_device_id' => $device->id,
        'job_id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
        'message' => 'Halo',
        'status' => 'completed',
        'total' => 3,
        'queued' => 0,
        'sent' => 1,
        'failed' => 2,
        'cancelled' => 0,
        'phones' => ['628111111111', '628222222222', '628333333333'],
    ]);

    Http::fake([
        '*/wa-blast/a1b2c3d4-e5f6-7890-abcd-ef1234567890/failed' => Http::response([
            'status' => 'success',
            'response' => [
                'campaignId' => $campaign->job_id,
                'totalFailed' => 2,
                'failed' => [
                    [
                        'index' => 3,
                        'phone' => '628111111111',
                        'status' => 'failed',
                        'error' => 'Phone number is not registered on WhatsApp',
                        'updatedAt' => now()->toIso8601String(),
                    ],
                    [
                        'index' => 7,
                        'phone' => '628222222222',
                        'status' => 'failed',
                        'error' => 'Timeout',
                        'updatedAt' => now()->toIso8601String(),
                    ],
                ],
            ],
        ]),
        '*/wa-blast/a1b2c3d4-e5f6-7890-abcd-ef1234567890/retry-failed' => Http::response([
            'status' => 'success',
            'response' => ['retried' => 1],
        ]),
        '*/wa-blast/a1b2c3d4-e5f6-7890-abcd-ef1234567890' => Http::response([
            'status' => 'success',
            'response' => [
                'id' => $campaign->job_id,
                'status' => 'processing',
                'total' => 3,
                'queued' => 1,
                'sent' => 1,
                'failed' => 1,
                'cancelled' => 0,
            ],
        ]),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.blasts.show', $campaign))
        ->assertOk()
        ->assertSee('628111111111')
        ->assertSee('Phone number is not registered on WhatsApp');

    $this->actingAs($user)
        ->post(route('dashboard.blasts.retry-failed', $campaign), [
            'indexes' => [3],
        ])
        ->assertRedirect(route('dashboard.blasts.show', $campaign))
        ->assertSessionHas('success');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/retry-failed')
            && $request['indexes'] === [3];
    });
});
