<?php

declare(strict_types=1);

/**
 * This file is part of the SimplexCalculator library
 *
 * Copyright (c) 2014 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 *
 * @link     https://github.com/uestla/Simplex-Calculator
 */

namespace Simplex;

class Helpers
{
    public static function gcd(int $a, int $b): int
    {
        if (!self::isInt($a) || !self::isInt($b)) {
            throw new \InvalidArgumentException('Integers expected for gcd.');
        }

        $a = (int)abs($a);
        $b = (int)abs($b);

        if ($a === 0 && $b === 0) {
            throw new \InvalidArgumentException('At least one number must not be a zero.');
        }

        if ($a === 0) {
            return abs($b);
        }
        if ($b === 0) {
            return abs($a);
        }

        return abs(self::gcdRecursive($a, $b));
    }

    /**
     * @return int -1, 0, 1
     */
    public static function sgn(float $n): int
    {
        return $n < 0 ? -1 : ($n > 0 ? 1 : 0);
    }

    public static function isInt($n): bool
    {
        return is_numeric($n) && round($n) === (float)$n;
    }

    private static function gcdRecursive(int $a, int $b): int
    {
        return $a % $b ? self::gcdRecursive($b, $a % $b) : $b;
    }
}
