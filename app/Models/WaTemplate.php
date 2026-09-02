<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaTemplate extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
        'body',
        'status',
        'usage_count',
    ];

    /**
     * Parse the template body by replacing {placeholder} with actual values.
     *
     * @param  array<string, string>  $variables
     */
    public function parsedBody(array $variables = []): string
    {
        $body = $this->body;

        foreach ($variables as $key => $value) {
            $body = str_replace('{'.$key.'}', $value, $body);
        }

        return $body;
    }

    /**
     * Get example variables used for preview.
     *
     * @return array<string, string>
     */
    public function exampleVariables(): array
    {
        preg_match_all('/\{(\w+)\}/', $this->body, $matches);

        $examples = [
            'nama' => 'Budi Santoso',
            'tanggal' => now()->translatedFormat('d F Y'),
            'waktu' => now()->format('H:i'),
            'dokter' => 'dr. Siti Aminah, Sp.PD',
            'poli' => 'Poli Penyakit Dalam',
            'no_rm' => 'RM-20260001',
            'antrian' => 'A-015',
            'ruang' => 'Ruang 3A',
            'hasil' => 'Normal',
        ];

        $result = [];
        foreach ($matches[1] as $placeholder) {
            $result[$placeholder] = $examples[$placeholder] ?? '[' . $placeholder . ']';
        }

        return $result;
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }
}
