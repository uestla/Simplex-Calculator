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
	 * @param  numeric $a
	 * @param  numeric $b
	 * @return numeric-string
	 */
	public static function gcd($a, $b)
	{
		if (!self::isInt($a) || !self::isInt($b)) {
			throw new \InvalidArgumentException('Integers expected for gcd.');
		}

		$a = (string) $a;
		$b = (string) $b;

		$aZero = Math::comp($a, '0') === 0;
		$bZero = Math::comp($b, '0') === 0;

		if ($aZero && $bZero) {
			throw new \InvalidArgumentException('At least one number must not be a zero.');
		}

		if ($aZero) {
			return $b;
		}

		if ($bZero) {
			return $a;
		}

		$gcd = '0';

		while (true) {
			$mod = Math::mod($a, $b);

			if (Math::comp($mod, '0') === 0) {
				$gcd = $b;
				break;
			}

			$a = $b;
			$b = $mod;
		}

		return Math::mul($gcd, (string) self::sgn($gcd)); // abs
	}


	/**
	 * @param  numeric $n
	 * @return int -1, 0, 1
	 */
	public static function sgn($n)
	{
		return Math::comp((string) $n, '0');
	}


	/**
	 * @param  mixed $n
	 * @return bool
	 */
	public static function isInt($n)
	{
		if (!is_numeric($n)) {
			return false;
		}

		$dotParts = explode('.', (string) $n);

		// either no decimal part or only filled with zeros
		return !isset($dotParts[1]) || str_replace('0', '', $dotParts[1]) === '';
	}

}
