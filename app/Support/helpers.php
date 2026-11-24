<?php

if (! function_exists('rp')) {
    /** Format Rupiah penuh: rp(1250000) => "Rp1.250.000" */
    function rp(int|float|null $value): string
    {
        return 'Rp'.number_format((float) $value, 0, ',', '.');
    }
}

if (! function_exists('rp_compact')) {
    /** Format Rupiah ringkas: rp_compact(1250000000) => "1,3 M" */
    function rp_compact(int|float|null $value): string
    {
        $value = (float) $value;
        if ($value >= 1e9) {
            return rtrim(rtrim(number_format($value / 1e9, 2, ',', '.'), '0'), ',').' M';
        }
        if ($value >= 1e6) {
            return rtrim(rtrim(number_format($value / 1e6, 1, ',', '.'), '0'), ',').' jt';
        }

        return rp($value);
    }
}

if (! function_exists('pct_of')) {
    function pct_of(int|float $part, int|float $total): int
    {
        return $total > 0 ? (int) round(100 * $part / $total) : 0;
    }
}
