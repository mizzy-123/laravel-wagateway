<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWhatsappSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'base_url' => ['required', 'url', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
            'webhook_url' => ['nullable', 'url', 'max:255'],
            'webhook_secret' => ['nullable', 'string', 'max:255'],
            'connect_timeout' => ['required', 'integer', 'min:1', 'max:60'],
            'timeout' => ['required', 'integer', 'min:5', 'max:300'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'base_url.required' => 'Base URL WPPConnect wajib diisi.',
            'base_url.url' => 'Base URL tidak valid.',
            'webhook_url.url' => 'Webhook URL tidak valid.',
            'connect_timeout.required' => 'Connect timeout wajib diisi.',
            'timeout.required' => 'HTTP timeout wajib diisi.',
        ];
    }
}
