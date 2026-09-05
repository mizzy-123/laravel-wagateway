<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaBlastCampaign extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'wa_device_id',
        'job_id',
        'message',
        'status',
        'total',
        'queued',
        'sent',
        'failed',
        'cancelled',
        'phones',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'queued',
        'total' => 0,
        'queued' => 0,
        'sent' => 0,
        'failed' => 0,
        'cancelled' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phones' => 'array',
            'total' => 'integer',
            'queued' => 'integer',
            'sent' => 'integer',
            'failed' => 'integer',
            'cancelled' => 'integer',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(WaDevice::class, 'wa_device_id');
    }

    public function syncFromRemote(array $payload): void
    {
        $data = $payload['response'] ?? $payload;

        $this->update([
            'status' => (string) ($data['status'] ?? $this->status),
            'total' => (int) ($data['total'] ?? $this->total),
            'queued' => (int) ($data['queued'] ?? $this->queued),
            'sent' => (int) ($data['sent'] ?? $this->sent),
            'failed' => (int) ($data['failed'] ?? $this->failed),
            'cancelled' => (int) ($data['cancelled'] ?? $this->cancelled),
        ]);
    }

    public function messagePreview(int $limit = 80): string
    {
        $text = preg_replace('/\s+/', ' ', trim($this->message)) ?? '';

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return mb_substr($text, 0, $limit).'…';
    }

    public function progressPercent(): int
    {
        if ($this->total <= 0) {
            return 0;
        }

        $done = $this->sent + $this->failed + $this->cancelled;

        return (int) min(100, round(($done / $this->total) * 100));
    }
}
