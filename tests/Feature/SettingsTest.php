<?php

use App\Models\User;
use App\Support\EnvEditor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('shows the settings page for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.settings'))
        ->assertOk()
        ->assertSee('Profil Administrator')
        ->assertSee('Koneksi WPPConnect');
});

it('updates the user profile', function () {
    $user = User::factory()->create([
        'name' => 'Admin Lama',
        'email' => 'lama@roemani.co.id',
    ]);

    $this->actingAs($user)
        ->put(route('dashboard.settings.profile'), [
            'name' => 'Admin Baru',
            'email' => 'baru@roemani.co.id',
        ])
        ->assertRedirect(route('dashboard.settings'))
        ->assertSessionHas('success');

    expect($user->fresh()->name)->toBe('Admin Baru')
        ->and($user->fresh()->email)->toBe('baru@roemani.co.id');
});

it('updates password when current password is correct', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user)
        ->put(route('dashboard.settings.profile'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect(route('dashboard.settings'));

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

it('saves whatsapp settings through the env editor', function () {
    $user = User::factory()->create();

    $this->mock(EnvEditor::class, function ($mock) {
        $mock->shouldReceive('update')
            ->once()
            ->withArgs(function (array $values) {
                return $values['WA_BASE_URL'] === 'http://127.0.0.1:21465'
                    && $values['WA_HTTP_TIMEOUT'] === 45;
            });
    });

    $this->actingAs($user)
        ->put(route('dashboard.settings.whatsapp'), [
            'base_url' => 'http://127.0.0.1:21465',
            'secret_key' => 'NEW_SECRET',
            'webhook_url' => 'http://localhost/api/webhook/whatsapp',
            'webhook_secret' => 'hook-secret',
            'connect_timeout' => 5,
            'timeout' => 45,
        ])
        ->assertRedirect(route('dashboard.settings'))
        ->assertSessionHas('success');
});

it('reports connection test result', function () {
    $user = User::factory()->create();

    Http::fake([
        '*' => Http::response(['status' => 'ok'], 200),
    ]);

    $this->actingAs($user)
        ->post(route('dashboard.settings.test-connection'))
        ->assertRedirect(route('dashboard.settings'))
        ->assertSessionHas('success');
});
