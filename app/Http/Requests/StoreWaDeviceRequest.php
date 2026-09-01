<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWaDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'session' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9_]+$/',
                Rule::unique('wa_devices', 'session'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama perangkat wajib diisi.',
            'session.required' => 'Session ID wajib diisi.',
            'session.regex' => 'Session ID hanya boleh huruf, angka, dan underscore.',
            'session.unique' => 'Session ID sudah digunakan.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('session')) {
            $this->merge([
                'session' => strtoupper((string) $this->input('session')),
            ]);
        }
    }
}
