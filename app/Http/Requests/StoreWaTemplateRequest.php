<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaTemplateRequest extends FormRequest
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
            'category' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string'],
            'status' => ['required', 'string', 'in:active,draft'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama template wajib diisi.',
            'category.required' => 'Kategori wajib dipilih.',
            'body.required' => 'Isi pesan template wajib diisi.',
            'status.in' => 'Status harus active atau draft.',
        ];
    }
}
