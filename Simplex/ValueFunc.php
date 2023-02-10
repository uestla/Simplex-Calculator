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


final class ValueFunc extends VariableSet
{

	/** @var Fraction */
	private $value;


	/**
	 * @param  array $set
	 * @param  Fraction|numeric $value
	 */
	public function __construct(array $set, $value)
	{
		parent::__construct($set);

		$this->value = Fraction::create($value);
	}


	/** @return Fraction */
	public function getValue()
	{
		return $this->value;
	}


	/** Deep copy */
	public function __clone()
	{
		parent::__clone();

		if (is_object($this->value)) {
			$this->value = clone $this->value;
		}
	}

}
