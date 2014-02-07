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


class VariableSet
{

	/** @var array */
	protected $set;



	/** @param  array $set [ varname => fraction ] */
	function __construct(array $set)
	{
		foreach ($set as $var => $coeff) {
			$set[$var] = Fraction::create($coeff);
		}

		ksort($set);
		$this->set = $set;
	}



	/** @return array */
	function getSet()
	{
		return $this->set;
	}



	/** @return Fraction|NULL */
	function getMin()
	{
		$min = NULL;

		foreach ($this->set as $var => $coeff) {
			if ($min === NULL || $coeff->isLowerThan($min)) {
				$min = $coeff;
			}
		}

		return $min;
	}



	/** @return string[] */
	function getVariableList()
	{
		return array_keys($this->set);
	}



	/**
	 * @param  string $var
	 * @return bool
	 */
	function has($var)
	{
		return isset($this->set[$var]);
	}



	/**
	 * @param  string $var
	 * @return Fraction
	 */
	function get($var)
	{
		return $this->set[$var];
	}



	/** Deep copy */
	function __clone()
	{
		foreach ($this->set as $var => $coeff) {
			$this->set[$var] = clone $coeff;
		}
	}

}
