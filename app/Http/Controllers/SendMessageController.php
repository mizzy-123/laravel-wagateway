<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\WaDevice;
use App\Models\WaMessage;
use App\Models\WaTemplate;
use App\Services\WppConnectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class SendMessageController extends Controller
{
    public function __construct(private WppConnectService $wppConnect) {}

    public function index(): View
    {
        return view('dashboard.send.index', [
            'devices' => WaDevice::query()->where('status', 'connected')->get(),
            'templates' => WaTemplate::query()->where('status', 'active')->latest()->get(),
            'appointmentCounts' => [
                'all' => Appointment::validPhone()->count(),
                'patient' => Appointment::where('type', 'patient')->validPhone()->count(),
                'doctor' => Appointment::where('type', 'doctor')->validPhone()->count(),
                'employee' => Appointment::where('type', 'employee')->validPhone()->count(),
            ],
        ]);
    }

    public function searchAppointments(Request $request): JsonResponse
    {
        $type = $request->query('type', 'all');
        $q = trim((string) $request->query('q', ''));
        $limit = min(max((int) $request->query('limit', 20), 1), 100);

        $query = Appointment::query()->validPhone();

        if ($type !== 'all' && in_array($type, ['patient', 'doctor', 'employee'], true)) {
            $query->where('type', $type);
        }

        if ($q !== '') {
            $cleanDigits = preg_replace('/\D/', '', $q);
            $query->where(function ($sub) use ($q, $cleanDigits) {
                $sub->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($q) . '%']);
                if ($cleanDigits !== '') {
                    $sub->orWhere('phone', 'LIKE', "%{$cleanDigits}%");
                }
                $sub->orWhere('patient_id', 'LIKE', "%{$q}%")
                    ->orWhere('doctor_id', 'LIKE', "%{$q}%")
                    ->orWhere('employee_id', 'LIKE', "%{$q}%");
            });
        }

        $items = $query->orderBy('name')
            ->limit($limit)
            ->get(['id', 'type', 'name', 'phone', 'patient_id', 'doctor_id', 'employee_id']);

        return response()->json([
            'status' => 'success',
            'data' => $items->map(fn (Appointment $item) => [
                'id' => $item->id,
                'type' => $item->type,
                'type_label' => $item->type_label,
                'name' => $item->name ?? '-',
                'ref_id' => $item->patient_id ?? $item->doctor_id ?? $item->employee_id ?? '-',
                'phone' => $item->phone,
                'formatted_phone' => $item->formatted_phone,
                'normalized_phone' => $item->normalized_phone,
            ]),
        ]);
    }

    public function loadAppointmentNumbers(Request $request): JsonResponse
    {
        $type = $request->query('type', 'all');
        $limit = $request->query('limit', 'all');

        $query = Appointment::query()->validPhone();

        if ($type !== 'all' && in_array($type, ['patient', 'doctor', 'employee'], true)) {
            $query->where('type', $type);
        }

        if (is_numeric($limit) && (int) $limit > 0) {
            $query->limit((int) $limit);
        }

        $phones = $query->pluck('phone')
            ->map(function ($phone) {
                $digits = preg_replace('/\D/', '', (string) $phone);
                if (str_starts_with($digits, '0')) {
                    return '62' . substr($digits, 1);
                }
                return $digits;
            })
            ->filter(fn ($phone) => strlen($phone) >= 9)
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'status' => 'success',
            'type' => $type,
            'count' => count($phones),
            'phones' => $phones,
        ]);
    }

    public function single(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'exists:wa_devices,id'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:4096'],
        ], [
            'device_id.required' => 'Pilih perangkat pengirim.',
            'device_id.exists' => 'Perangkat tidak ditemukan.',
            'phone.required' => 'Nomor tujuan wajib diisi.',
            'message.required' => 'Isi pesan wajib diisi.',
        ]);

        $device = WaDevice::findOrFail($data['device_id']);

        // Normalize phone: strip non-digits, ensure starts with country code
        $phone = preg_replace('/\D/', '', $data['phone']);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $this->wppConnect->sendMessage(
                session: $device->session,
                token: $device->token ?? '',
                phone: $phone . '@c.us',
                message: $data['message'],
            );

            WaMessage::query()->create([
                'wa_device_id' => $device->id,
                'direction' => 'outbound',
                'to_number' => $phone,
                'body' => $data['message'],
                'type' => 'chat',
                'status' => 'sent',
            ]);

            // Increment template usage if a template was used
            if ($request->filled('template_id')) {
                WaTemplate::find($request->integer('template_id'))?->incrementUsage();
            }

            return redirect()
                ->route('dashboard.send')
                ->with('success', 'Pesan berhasil dikirim ke ' . $phone . '.');
        } catch (RuntimeException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }

    public function blast(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'exists:wa_devices,id'],
            'phones' => ['required', 'string'],
            'message' => ['required', 'string', 'max:4096'],
        ], [
            'device_id.required' => 'Pilih perangkat pengirim.',
            'phones.required' => 'Daftar nomor tujuan wajib diisi.',
            'message.required' => 'Isi pesan wajib diisi.',
        ]);

        $device = WaDevice::findOrFail($data['device_id']);

        // Parse & normalize phone list (comma or newline separated)
        $rawPhones = preg_split('/[\s,]+/', $data['phones']);
        $phones = collect($rawPhones)
            ->map(fn ($p) => preg_replace('/\D/', '', trim($p)))
            ->filter()
            ->map(function ($p) {
                if (str_starts_with($p, '0')) {
                    return '62' . substr($p, 1);
                }
                return $p;
            })
            ->unique()
            ->values()
            ->toArray();

        if (empty($phones)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada nomor valid yang ditemukan.');
        }

        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($phones as $phone) {
            try {
                $this->wppConnect->sendMessage(
                    session: $device->session,
                    token: $device->token ?? '',
                    phone: $phone . '@c.us',
                    message: $data['message'],
                );

                WaMessage::query()->create([
                    'wa_device_id' => $device->id,
                    'direction' => 'outbound',
                    'to_number' => $phone,
                    'body' => $data['message'],
                    'type' => 'chat',
                    'status' => 'sent',
                ]);

                $sent++;
            } catch (RuntimeException $e) {
                $failed++;
                $errors[] = $phone . ': ' . $e->getMessage();
            }
        }

        if ($request->filled('template_id')) {
            WaTemplate::find($request->integer('template_id'))?->increment('usage_count', $sent);
        }

        $summary = "Blast selesai: {$sent} berhasil" . ($failed > 0 ? ", {$failed} gagal." : '.');

        return redirect()
            ->route('dashboard.send')
            ->with($failed === 0 ? 'success' : 'warning', $summary);
    }
}
