<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWaTemplateRequest;
use App\Http\Requests\UpdateWaTemplateRequest;
use App\Models\WaTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class WaTemplateController extends Controller
{
    public function store(StoreWaTemplateRequest $request): RedirectResponse
    {
        WaTemplate::query()->create($request->validated());

        return redirect()
            ->route('dashboard.templates')
            ->with('success', 'Template berhasil dibuat.');
    }

    public function update(UpdateWaTemplateRequest $request, WaTemplate $template): RedirectResponse
    {
        $template->update($request->validated());

        return redirect()
            ->route('dashboard.templates')
            ->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(WaTemplate $template): RedirectResponse
    {
        $template->delete();

        return redirect()
            ->route('dashboard.templates')
            ->with('success', 'Template berhasil dihapus.');
    }

    public function preview(WaTemplate $template): JsonResponse
    {
        $variables = $template->exampleVariables();

        return response()->json([
            'name' => $template->name,
            'category' => $template->category,
            'body' => $template->body,
            'parsed' => $template->parsedBody($variables),
            'variables' => $variables,
        ]);
    }
}
