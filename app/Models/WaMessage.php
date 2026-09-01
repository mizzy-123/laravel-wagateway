<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaMessage extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'wa_device_id',
        'wa_message_id',
        'direction',
        'from_number',
        'to_number',
        'body',
        'type',
        'status',
        'notify_name',
        'is_group',
        'raw_payload',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_group' => 'boolean',
            'raw_payload' => 'array',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(WaDevice::class, 'wa_device_id');
    }

    public function displayName(): string
    {
        return $this->notify_name
            ?? $this->raw_payload['notifyName']
            ?? $this->from_number
            ?? '-';
    }

    public function displayPhone(): string
    {
        $number = $this->direction === 'inbound'
            ? $this->from_number
            : $this->to_number;

        return $this->formatPhone($number);
    }

    private function formatPhone(?string $number): string
    {
        if ($number === null) {
            return '-';
        }

        $digits = preg_replace('/@.*/', '', $number) ?? $number;

        if (str_starts_with($digits, '62')) {
            return '+'.substr($digits, 0, 2).' '.substr($digits, 2);
        }

        return $digits;
    }
}
