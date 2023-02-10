<?php

/**
 * This file is part of the SimplexCalculator library
 *
 * Copyright (c) 2014 Petr Kessler (https://kesspess.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Simplex-Calculator
 */

namespace Simplex;


final class Helpers
{

	/**
	 * @param  int $a
	 * @param  int $b
	 * @return int
	 */
	public static function gcd($a, $b)
	{
		if (!self::isInt($a) || !self::isInt($b)) {
			throw new \InvalidArgumentException('Integers expected for gcd.');
		}

		$a = (int) abs($a);
		$b = (int) abs($b);

		if ($a === 0 && $b === 0) {
			throw new \InvalidArgumentException('At least one number must not be a zero.');
		}

		if ($a === 0) return $b;
		if ($b === 0) return $a;

		return self::gcdRecursive($a, $b);
	}


	/**
	 * @param  int $a
	 * @param  int $b
	 * @return int
	 */
	private static function gcdRecursive($a, $b)
	{
		return ($a % $b) ? self::gcdRecursive($b, $a % $b) : $b;
	}


	/**
	 * @param  numeric $n
	 * @return int -1, 0, 1
	 */
	public static function sgn($n)
	{
		return $n < 0 ? -1 : ($n > 0 ? 1 : 0);
	}


	/**
	 * @param  numeric $n
	 * @return bool
	 */
	public static function isInt($n)
	{
		return is_numeric($n) && round($n) === (float) $n;
	}

}
