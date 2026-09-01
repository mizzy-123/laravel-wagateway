<?php

namespace App\Http\Controllers;

use App\Models\WaDevice;
use App\Models\WaMessage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $devices = WaDevice::query()->latest()->get();
        $connectedCount = $devices->where('status', 'connected')->count();
        $messagesToday = WaMessage::query()->whereDate('created_at', today())->count();
        $failedToday = WaMessage::query()
            ->whereDate('created_at', today())
            ->where('status', 'failed')
            ->count();
        $successRate = $messagesToday > 0
            ? number_format((($messagesToday - $failedToday) / $messagesToday) * 100, 1).'%'
            : '100%';

        return view('dashboard.index', [
            'stats' => [
                [
                    'label' => 'Pesan Hari Ini',
                    'value' => number_format($messagesToday),
                    'change' => $messagesToday > 0 ? 'Data real-time' : 'Belum ada pesan',
                    'trend' => 'neutral',
                    'icon' => 'message',
                    'color' => 'brand',
                ],
                [
                    'label' => 'Perangkat Aktif',
                    'value' => $connectedCount.' / '.$devices->count(),
                    'change' => ($devices->count() - $connectedCount).' offline',
                    'trend' => 'neutral',
                    'icon' => 'device',
                    'color' => 'wa',
                ],
                [
                    'label' => 'Tingkat Sukses',
                    'value' => $successRate,
                    'change' => 'Hari ini',
                    'trend' => 'up',
                    'icon' => 'check',
                    'color' => 'emerald',
                ],
                [
                    'label' => 'Total Perangkat',
                    'value' => (string) $devices->count(),
                    'change' => 'Terdaftar',
                    'trend' => 'neutral',
                    'icon' => 'queue',
                    'color' => 'amber',
                ],
            ],
            'weeklyMessages' => $this->weeklyMessages(),
            'recentMessages' => $this->recentMessages(),
            'devices' => $devices,
        ]);
    }

    public function devices(): View
    {
        return view('dashboard.devices.index', [
            'devices' => WaDevice::query()->latest()->get(),
        ]);
    }

    public function messages(): View
    {
        return view('dashboard.messages.index', [
            'messages' => WaMessage::query()
                ->with('device')
                ->latest()
                ->limit(50)
                ->get(),
        ]);
    }

    public function templates(): View
    {
        return view('dashboard.templates.index', [
            'templates' => [
                [
                    'name' => 'Konfirmasi Janji Temu',
                    'category' => 'Appointment',
                    'status' => 'active',
                    'usage' => 342,
                    'updated_at' => '2 jam lalu',
                ],
                [
                    'name' => 'Hasil Lab Tersedia',
                    'category' => 'Laboratorium',
                    'status' => 'active',
                    'usage' => 218,
                    'updated_at' => '1 hari lalu',
                ],
                [
                    'name' => 'Pengingat Kontrol',
                    'category' => 'Follow-up',
                    'status' => 'active',
                    'usage' => 156,
                    'updated_at' => '3 hari lalu',
                ],
                [
                    'name' => 'Info Antrian Poli',
                    'category' => 'Informasi',
                    'status' => 'draft',
                    'usage' => 0,
                    'updated_at' => '5 hari lalu',
                ],
                [
                    'name' => 'Survei Kepuasan Pasien',
                    'category' => 'Survey',
                    'status' => 'active',
                    'usage' => 89,
                    'updated_at' => '1 minggu lalu',
                ],
            ],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function weeklyMessages(): array
    {
        $days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        return collect(range(6, 0))->map(function (int $daysAgo) use ($days) {
            $date = now()->subDays($daysAgo);

            return [
                'day' => $days[$date->dayOfWeek],
                'sent' => WaMessage::query()
                    ->whereDate('created_at', $date)
                    ->where('direction', 'outbound')
                    ->count(),
                'failed' => WaMessage::query()
                    ->whereDate('created_at', $date)
                    ->where('status', 'failed')
                    ->count(),
            ];
        })->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, WaMessage>
     */
    private function recentMessages()
    {
        return WaMessage::query()
            ->with('device')
            ->latest()
            ->limit(6)
            ->get();
    }
}
