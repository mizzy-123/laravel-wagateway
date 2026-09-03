<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $guarded = ['id'];

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
