<?php

use App\Models\User;
use App\Models\WaDevice;
use App\Models\WaMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

it('searches messages and devices from the topbar', function () {
    $user = User::factory()->create();

    $device = WaDevice::query()->create([
        'name' => 'Front Office RS',
        'session' => 'FRONT_OFFICE',
        'status' => 'connected',
        'phone' => '628111111111',
    ]);

    WaMessage::query()->create([
        'wa_device_id' => $device->id,
        'direction' => 'outbound',
        'to_number' => '628222222222',
        'body' => 'Konfirmasi janji temu Budi Santoso',
        'type' => 'chat',
        'status' => 'sent',
    ]);

    $response = $this->actingAs($user)
        ->getJson(route('dashboard.search', ['q' => 'Budi']));

    $response->assertOk()
        ->assertJsonPath('status', 'success');

    $groups = collect($response->json('groups'));
    expect($groups->pluck('key'))->toContain('messages');
});

it('returns notifications for disconnected devices and failed messages', function () {
    $user = User::factory()->create();

    $device = WaDevice::query()->create([
        'name' => 'Poli Umum',
        'session' => 'POLI_UMUM',
        'status' => 'disconnected',
    ]);

    WaMessage::query()->create([
        'wa_device_id' => $device->id,
        'direction' => 'outbound',
        'to_number' => '628333333333',
        'body' => 'Pesan gagal',
        'type' => 'chat',
        'status' => 'failed',
    ]);

    $this->actingAs($user)
        ->getJson(route('dashboard.notifications'))
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('unread_count', fn ($count) => $count >= 1)
        ->assertJsonFragment(['title' => 'Perangkat terputus'])
        ->assertJsonFragment(['title' => 'Pesan gagal dikirim']);
});

it('marks notifications as read', function () {
    $user = User::factory()->create();

    WaDevice::query()->create([
        'name' => 'Lab',
        'session' => 'LAB',
        'status' => 'disconnected',
    ]);

    $this->actingAs($user)
        ->getJson(route('dashboard.notifications'))
        ->assertJsonPath('unread_count', fn ($count) => $count >= 1);

    $this->actingAs($user)
        ->postJson(route('dashboard.notifications.read'))
        ->assertOk()
        ->assertJsonPath('unread_count', 0);

    expect(Cache::has("dashboard.notifications.read_at.{$user->id}"))->toBeTrue();
});
