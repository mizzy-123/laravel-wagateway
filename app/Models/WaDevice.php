<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaDevice extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'session',
        'phone',
        'status',
        'token',
        'qrcode',
        'session_status',
        'connected_at',
        'last_seen_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'connected_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WaMessage::class);
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function isConnecting(): bool
    {
        return $this->status === 'connecting';
    }

    public function messagesTodayCount(): int
    {
        return $this->messages()
            ->whereDate('created_at', today())
            ->count();
    }

    public function lastSeenLabel(): string
    {
        if ($this->last_seen_at === null) {
            return '-';
        }

        if ($this->last_seen_at->diffInMinutes(now()) < 1) {
            return 'Baru saja';
        }

        return $this->last_seen_at->diffForHumans();
    }
}
