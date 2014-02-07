<?php

/**
 * This file is part of the SimplexCalculator library
 *
 * Copyright (c) 2014 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Dual-Simplex-Calculator
 */

namespace Simplex;


class TableRow extends VariableSet
{

	/** @var string */
	private $var;

	/** @var Fraction */
	private $b;



	/**
	 * @param  string $var
	 * @param  array $set
	 * @param  Fraction|numeric $b
	 */
	function __construct($var, array $set, $b)
	{
		parent::__construct($set);

		$this->var = (string) $var;
		$this->b = Fraction::create($b);
	}



	/** @return string */
	function getVar()
	{
		return $this->var;
	}



	/** @return Fraction */
	function getB()
	{
		return $this->b;
	}



	/** Deep copy */
	function __clone()
	{
		$this->b = clone $this->b;
	}

}
