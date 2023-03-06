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


final class Math
{

	/**
	 * @param  numeric-string $a
	 * @param  numeric-string $b
	 * @return numeric-string
	 */
	public static function add($a, $b)
	{
		return bcadd($a, $b, 0);
	}


	/**
	 * @param  numeric-string $a
	 * @param  numeric-string $b
	 * @return numeric-string
	 */
	public static function sub($a, $b)
	{
		return bcsub($a, $b, 0);
	}


	/**
	 * @param  numeric-string $a
	 * @param  numeric-string $b
	 * @return numeric-string
	 */
	public static function mul($a, $b)
	{
		return bcmul($a, $b, 0);
	}


	/**
	 * @param  numeric-string $a
	 * @param  numeric-string $b
	 * @return numeric-string
	 */
	public static function div($a, $b)
	{
		$res = bcdiv($a, $b, 0);
		assert(is_string($res));
		return $res;
	}


	/**
	 * @param  numeric-string $a
	 * @param  numeric-string $b
	 * @return numeric-string
	 */
	public static function pow($a, $b)
	{
		return bcpow($a, $b, 0);
	}


	/**
	 * @param  numeric-string $a
	 * @param  numeric-string $b
	 * @return numeric-string
	 */
	public static function mod($a, $b)
	{
		$res = PHP_VERSION_ID >= 70200
			? bcmod($a, $b, 0) // $scale argument added since PHP 7.2
			: bcmod($a, $b);

		assert(is_string($res));
		return $res;
	}


	/**
	 * @param  numeric-string $a
	 * @param  numeric-string $b
	 * @return int
	 */
	public static function comp($a, $b)
	{
		return bccomp($a, $b, 0);
	}

}
