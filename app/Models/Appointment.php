<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $guarded = ['id'];

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'patient' => 'Pasien',
            'doctor' => 'Dokter',
            'employee' => 'Karyawan',
            default => ucfirst((string) $this->type),
        };
    }

    public function getFormattedPhoneAttribute(): string
    {
        return $this->formatPhone($this->phone);
    }

    public function getNormalizedPhoneAttribute(): string
    {
        $digits = preg_replace('/\D/', '', (string) $this->phone);
        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }
        return $digits;
    }

    public function scopeValidPhone($query)
    {
        return $query->whereNotNull('phone')->where('phone', '!=', '');
    }

    public function formatPhone(?string $number): string
    {
        if ($number === null || $number === '') {
            return '-';
        }

        $digits = preg_replace('/@.*/', '', $number) ?? $number;

        if (str_starts_with($digits, '62')) {
            return '+'.substr($digits, 0, 2).' '.substr($digits, 2);
        }

        if (str_starts_with($digits, '08')) {
            return '08'.substr($digits, 2);
        }

        return $digits;
    }
}
