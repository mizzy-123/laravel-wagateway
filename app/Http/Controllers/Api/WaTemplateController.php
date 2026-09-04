<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWaTemplateRequest;
use App\Models\WaTemplate;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Templates', weight: 1)]
class WaTemplateController extends Controller
{
    /**
     * Buat template pesan WhatsApp baru.
     */
    #[Endpoint(
        operationId: 'createWaTemplate',
        title: 'Create template pesan',
        description: 'Membuat template pesan WhatsApp untuk notifikasi pasien. Placeholder seperti `{nama}` dan `{tanggal}` boleh dipakai di body. Jika `status` tidak dikirim, default-nya `draft`.',
    )]
    public function store(StoreWaTemplateRequest $request): JsonResponse
    {
        $template = WaTemplate::query()->create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Template berhasil dibuat.',
            'data' => [
                'id' => $template->id,
                'name' => $template->name,
                'category' => $template->category,
                'body' => $template->body,
                'status' => $template->status,
                'usage_count' => $template->usage_count,
                'created_at' => $template->created_at?->toIso8601String(),
                'updated_at' => $template->updated_at?->toIso8601String(),
            ],
        ], 201);
    }
}
