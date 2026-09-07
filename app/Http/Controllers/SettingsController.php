<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateWhatsappSettingsRequest;
use App\Support\EnvEditor;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use RuntimeException;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('dashboard.settings.index', [
            'user' => Auth::user(),
            'whatsapp' => [
                'base_url' => config('whatsapp.base_url'),
                'secret_key' => config('whatsapp.secret_key'),
                'webhook_url' => config('whatsapp.webhook_url'),
                'webhook_secret' => config('whatsapp.webhook_secret'),
                'connect_timeout' => config('whatsapp.http.connect_timeout'),
                'timeout' => config('whatsapp.http.timeout'),
            ],
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = $request->string('password')->toString();
        }

        $user->update($data);

        return redirect()
            ->route('dashboard.settings')
            ->with('success', 'Profil berhasil diperbarui.')
            ->with('settings_tab', 'profile');
    }

    public function updateWhatsapp(UpdateWhatsappSettingsRequest $request, EnvEditor $envEditor): RedirectResponse
    {
        $validated = $request->validated();

        $envValues = [
            'WA_BASE_URL' => rtrim((string) $validated['base_url'], '/'),
            'WA_WEBHOOK_URL' => $validated['webhook_url'] ?? '',
            'WA_HTTP_CONNECT_TIMEOUT' => (int) $validated['connect_timeout'],
            'WA_HTTP_TIMEOUT' => (int) $validated['timeout'],
        ];

        if ($request->filled('secret_key')) {
            $envValues['WA_SECRET_KEY'] = (string) $validated['secret_key'];
        }

        if ($request->filled('webhook_secret')) {
            $envValues['WA_WEBHOOK_SECRET'] = (string) $validated['webhook_secret'];
        }

        try {
            $envEditor->update($envValues);
            Artisan::call('config:clear');
        } catch (RuntimeException $e) {
            return redirect()
                ->route('dashboard.settings')
                ->with('error', $e->getMessage())
                ->with('settings_tab', 'whatsapp');
        }

        return redirect()
            ->route('dashboard.settings')
            ->with('success', 'Pengaturan WhatsApp Gateway berhasil disimpan.')
            ->with('settings_tab', 'whatsapp');
    }

    public function testConnection(): RedirectResponse
    {
        $baseUrl = config('whatsapp.base_url');

        try {
            $response = Http::connectTimeout((int) config('whatsapp.http.connect_timeout'))
                ->timeout((int) config('whatsapp.http.timeout'))
                ->acceptJson()
                ->get(rtrim((string) $baseUrl, '/').'/');

            $message = $response->successful()
                ? 'Koneksi ke WPPConnect berhasil (HTTP '.$response->status().').'
                : 'Server merespons dengan status HTTP '.$response->status().'.';

            return redirect()
                ->route('dashboard.settings')
                ->with($response->successful() ? 'success' : 'error', $message)
                ->with('settings_tab', 'whatsapp');
        } catch (ConnectionException $e) {
            return redirect()
                ->route('dashboard.settings')
                ->with('error', 'Gagal terhubung ke WPPConnect: '.$e->getMessage())
                ->with('settings_tab', 'whatsapp');
        }
    }
}
