<?php

namespace App\Helpers;

class VolumeHelper
{
    /**
     * Round a single volume (ml) to the nearest 10 mL using half-up rules.
     * Examples: 41->40, 44->40, 45->50, 49->50
     */
    public static function roundMl($value): int
    {
        if ($value === null || $value === '') return 0;
        $num = (float) $value;
        if (!is_finite($num)) return 0;
        if ($num < 0) $num = 0; // volumes cannot be negative
        // nearest 10 mL with halves up
        return (int) (round($num / 10) * 10);
    }

    /**
     * Round an array of volumes to nearest 10 mL and return as integers.
     */
    public static function roundMlArray($values): array
    {
        if (!is_array($values)) return [];
        return array_values(array_map(function ($v) {
            return self::roundMl($v);
        }, $values));
    }
}
