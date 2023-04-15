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


abstract class VariableSet
{

	/** @var array<string, Fraction> */
	protected $set;


	/** @param  array<string, Fraction|numeric> $set */
	public function __construct(array $set)
	{
		foreach ($set as $var => $coeff) {
			$set[$var] = Fraction::create($coeff);
		}

		ksort($set);
		$this->set = $set;
	}


	/** @return array<string, Fraction> */
	public function getSet()
	{
		return $this->set;
	}


	/** @return Fraction|null */
	public function getMin()
	{
		$min = null;

		foreach ($this->set as $coeff) {
			if ($min === null || $coeff->isLowerThan($min)) {
				$min = $coeff;
			}
		}

		return $min;
	}


	/** @return string[] */
	public function getVariableList()
	{
		return array_keys($this->set);
	}


	/**
	 * @param  string $var
	 * @return bool
	 */
	public function has($var)
	{
		return isset($this->set[$var]);
	}


	/**
	 * @param  string $var
	 * @return Fraction
	 */
	public function get($var)
	{
		return $this->set[$var];
	}


	/** Deep copy */
	public function __clone()
	{
		foreach ($this->set as $var => $coeff) {
			$this->set[$var] = clone $coeff;
		}
	}

}
