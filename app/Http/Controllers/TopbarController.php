<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\WaBlastCampaign;
use App\Models\WaDevice;
use App\Models\WaMessage;
use App\Models\WaTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TopbarController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([
                'status' => 'success',
                'query' => $q,
                'groups' => [],
            ]);
        }

        $like = '%'.Str::lower($q).'%';
        $digitQ = preg_replace('/\D/', '', $q) ?? '';

        $messages = WaMessage::query()
            ->with('device:id,name')
            ->where(function ($query) use ($like, $digitQ) {
                $query->whereRaw('LOWER(COALESCE(body, \'\')) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(to_number, \'\')) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(from_number, \'\')) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(notify_name, \'\')) LIKE ?', [$like]);

                if ($digitQ !== '') {
                    $query->orWhere('to_number', 'LIKE', "%{$digitQ}%")
                        ->orWhere('from_number', 'LIKE', "%{$digitQ}%");
                }
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (WaMessage $message) => [
                'title' => $message->displayName(),
                'subtitle' => Str::limit((string) ($message->body ?? '-'), 60),
                'meta' => ($message->device?->name ?? 'Perangkat').' · '.$message->created_at?->diffForHumans(),
                'url' => route('dashboard.messages'),
            ]);

        $devices = WaDevice::query()
            ->where(function ($query) use ($like, $digitQ) {
                $query->whereRaw('LOWER(name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(session) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(phone, \'\')) LIKE ?', [$like]);

                if ($digitQ !== '') {
                    $query->orWhere('phone', 'LIKE', "%{$digitQ}%");
                }
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (WaDevice $device) => [
                'title' => $device->name,
                'subtitle' => $device->session.($device->phone ? ' · '.$device->phone : ''),
                'meta' => ucfirst($device->status),
                'url' => route('dashboard.devices'),
            ]);

        $appointments = Appointment::query()
            ->validPhone()
            ->where(function ($query) use ($like, $digitQ, $q) {
                $query->whereRaw('LOWER(COALESCE(name, \'\')) LIKE ?', [$like])
                    ->orWhere('patient_id', 'LIKE', "%{$q}%")
                    ->orWhere('doctor_id', 'LIKE', "%{$q}%")
                    ->orWhere('employee_id', 'LIKE', "%{$q}%");

                if ($digitQ !== '') {
                    $query->orWhere('phone', 'LIKE', "%{$digitQ}%");
                }
            })
            ->orderBy('name')
            ->limit(5)
            ->get()
            ->map(fn (Appointment $item) => [
                'title' => $item->name ?? '-',
                'subtitle' => $item->formatted_phone,
                'meta' => $item->type_label,
                'url' => route('dashboard.send'),
            ]);

        $templates = WaTemplate::query()
            ->where(function ($query) use ($like) {
                $query->whereRaw('LOWER(name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(category) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(body) LIKE ?', [$like]);
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (WaTemplate $template) => [
                'title' => $template->name,
                'subtitle' => Str::limit($template->body, 60),
                'meta' => $template->category.' · '.$template->status,
                'url' => route('dashboard.templates'),
            ]);

        $blasts = WaBlastCampaign::query()
            ->with('device:id,name')
            ->where(function ($query) use ($like, $q) {
                $query->whereRaw('LOWER(message) LIKE ?', [$like])
                    ->orWhere('job_id', 'LIKE', "%{$q}%");
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (WaBlastCampaign $blast) => [
                'title' => Str::limit($blast->messagePreview(40), 40),
                'subtitle' => 'Job '.$blast->job_id,
                'meta' => ($blast->device?->name ?? '-').' · gagal '.$blast->failed,
                'url' => route('dashboard.blasts.show', $blast),
            ]);

        $groups = collect([
            ['key' => 'messages', 'label' => 'Pesan', 'items' => $messages],
            ['key' => 'devices', 'label' => 'Perangkat', 'items' => $devices],
            ['key' => 'appointments', 'label' => 'Kontak / Appointment', 'items' => $appointments],
            ['key' => 'templates', 'label' => 'Template', 'items' => $templates],
            ['key' => 'blasts', 'label' => 'Blast', 'items' => $blasts],
        ])->filter(fn (array $group) => $group['items']->isNotEmpty())
            ->values()
            ->all();

        return response()->json([
            'status' => 'success',
            'query' => $q,
            'groups' => $groups,
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $readAt = Cache::get($this->readCacheKey($userId));
        $items = $this->buildNotifications();

        $unreadCount = collect($items)
            ->filter(function (array $item) use ($readAt) {
                if ($readAt === null) {
                    return true;
                }

                return $item['created_at'] > $readAt;
            })
            ->count();

        return response()->json([
            'status' => 'success',
            'unread_count' => $unreadCount,
            'items' => $items,
        ]);
    }

    public function markNotificationsRead(Request $request): JsonResponse
    {
        Cache::put(
            $this->readCacheKey($request->user()->id),
            now()->toIso8601String(),
            now()->addDays(30),
        );

        return response()->json([
            'status' => 'success',
            'unread_count' => 0,
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildNotifications(): array
    {
        $items = [];

        WaDevice::query()
            ->whereIn('status', ['disconnected', 'connecting'])
            ->latest('updated_at')
            ->limit(5)
            ->get()
            ->each(function (WaDevice $device) use (&$items) {
                $items[] = [
                    'id' => 'device-'.$device->id,
                    'type' => 'device',
                    'title' => $device->status === 'connecting'
                        ? 'Perangkat sedang menghubungkan'
                        : 'Perangkat terputus',
                    'body' => $device->name.' ('.$device->session.')',
                    'url' => route('dashboard.devices'),
                    'created_at' => optional($device->updated_at)->toIso8601String(),
                    'time_label' => $device->updated_at?->diffForHumans() ?? '-',
                ];
            });

        WaMessage::query()
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (WaMessage $message) use (&$items) {
                $items[] = [
                    'id' => 'message-'.$message->id,
                    'type' => 'message',
                    'title' => 'Pesan gagal dikirim',
                    'body' => ($message->to_number ?? $message->from_number ?? '-').' · '.Str::limit((string) ($message->body ?? ''), 40),
                    'url' => route('dashboard.messages'),
                    'created_at' => optional($message->created_at)->toIso8601String(),
                    'time_label' => $message->created_at?->diffForHumans() ?? '-',
                ];
            });

        WaBlastCampaign::query()
            ->where('failed', '>', 0)
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (WaBlastCampaign $blast) use (&$items) {
                $items[] = [
                    'id' => 'blast-'.$blast->id,
                    'type' => 'blast',
                    'title' => 'Blast punya pesan gagal',
                    'body' => $blast->failed.' gagal dari '.$blast->total.' · '.$blast->messagePreview(40),
                    'url' => route('dashboard.blasts.show', $blast),
                    'created_at' => optional($blast->updated_at)->toIso8601String(),
                    'time_label' => $blast->updated_at?->diffForHumans() ?? '-',
                ];
            });

        return collect($items)
            ->sortByDesc('created_at')
            ->take(12)
            ->values()
            ->all();
    }

    private function readCacheKey(int $userId): string
    {
        return "dashboard.notifications.read_at.{$userId}";
    }
}
