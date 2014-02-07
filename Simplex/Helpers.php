<?php

/**
 * This file is part of the SimplexCalculator library
 *
 * Copyright (c) 2014 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Simplex-Calculator
 */

namespace Simplex;


class Helpers
{

	/**
	 * @param  int $a
	 * @param  int $b
	 * @return int
	 */
	static function gcd($a, $b)
	{
		if (!self::isInt($a) || !self::isInt($b)) {
			throw new \InvalidArgumentException('Integers expected for gcd.');
		}

		$a = (int) abs($a);
		$b = (int) abs($b);

		if ($a === 0 && $b === 0) {
			throw new \InvalidArgumentException('At least one number must not be a zero.');
		}

		if ($a === 0) return abs($b);
		if ($b === 0) return abs($a);

		while ($a !== $b) {
			if ($a > $b) {
				$a -= $b;

			} else {
				$b -= $a;
			}
		}

		return abs($a);
	}



	/**
	 * @param  numeric $n
	 * @return int -1, 0, 1
	 */
	static function sgn($n)
	{
		return $n < 0 ? -1 : ($n > 0 ? 1 : 0);
	}



	/**
	 * @param  numeric $n
	 * @return bool
	 */
	static function isInt($n)
	{
		return is_numeric($n) && round($n) === (float) $n;
	}

}
