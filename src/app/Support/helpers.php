<?php

use App\Support\Money;

if (! function_exists('money')) {
    /** Format a kobo amount for display. */
    function money(?int $kobo, string $currency = 'NGN'): string
    {
        return Money::format($kobo, $currency);
    }
}
