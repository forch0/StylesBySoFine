<?php

namespace App\Support;

class Money
{
    /** Format a kobo amount as ₦1,234.56 */
    public static function format(?int $kobo, string $currency = 'NGN'): string
    {
        $symbol = match ($currency) {
            'NGN' => '₦',
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            default => $currency.' ',
        };

        return $symbol.number_format(($kobo ?? 0) / 100, 2);
    }

    /** Convert a naira decimal amount to kobo. */
    public static function toKobo(float|string $naira): int
    {
        return (int) round(((float) $naira) * 100);
    }
}
