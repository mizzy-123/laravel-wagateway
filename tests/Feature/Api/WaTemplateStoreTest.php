<?php

use App\Models\User;
use App\Models\WaTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a message template and returns 201', function () {
    $user = User::factory()->create();

    $payload = [
        'name' => 'Konfirmasi Janji Temu',
        'category' => 'Appointment',
        'body' => "Halo {nama},\n\nJanji temu Anda pada {tanggal} pukul {waktu}.",
        'status' => 'active',
    ];

    $response = $this->actingAs($user)
        ->postJson('/api/templates', $payload);

    $response->assertCreated()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Template berhasil dibuat.')
        ->assertJsonPath('data.name', 'Konfirmasi Janji Temu')
        ->assertJsonPath('data.category', 'Appointment')
        ->assertJsonPath('data.status', 'active')
        ->assertJsonPath('data.usage_count', 0);

    $this->assertDatabaseHas('wa_templates', [
        'name' => 'Konfirmasi Janji Temu',
        'category' => 'Appointment',
        'status' => 'active',
    ]);
});

it('defaults status to draft when omitted', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/templates', [
            'name' => 'Info Antrian Poli',
            'category' => 'Informasi',
            'body' => 'Nomor antrian Anda: {antrian}',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.status', 'draft');

    $this->assertDatabaseHas('wa_templates', [
        'name' => 'Info Antrian Poli',
        'status' => 'draft',
    ]);
});

it('returns 422 when required fields are missing', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/templates', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'category', 'body']);

    expect(WaTemplate::query()->count())->toBe(0);
});

it('returns 422 when status is invalid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/templates', [
            'name' => 'Template Invalid',
            'category' => 'Survey',
            'body' => 'Isi pesan',
            'status' => 'archived',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

it('redirects guests to login when creating a template', function () {
    $response = $this->postJson('/api/templates', [
        'name' => 'Unauthorized Template',
        'category' => 'Informasi',
        'body' => 'Tidak boleh',
    ]);

    $response->assertUnauthorized();
});
