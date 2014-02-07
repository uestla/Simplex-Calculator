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


class Restriction extends VariableSet
{

	/** @var int */
	private $type;

	/** @var Fraction */
	private $limit;



	const TYPE_EQ = 1;
	const TYPE_LOE = 2;
	const TYPE_GOE = 4;



	/**
	 * @param  array $set
	 * @param  int $type
	 * @param  Fraction|numeric $limit
	 */
	function __construct(array $set, $type, $limit)
	{
		parent::__construct($set);

		$this->type = (int) $type;
		$this->limit = Fraction::create($limit);
	}



	/** @return int */
	function getType()
	{
		return $this->type;
	}



	/** @return Fraction */
	function getLimit()
	{
		return $this->limit;
	}



	/** @return Restriction */
	function fixRightSide()
	{
		if ($this->limit->isLowerThan(0)) {
			$set = array();
			foreach ($this->set as $var => $coeff) {
				$set[$var] = $coeff->multiply(-1);
			}

			$type = $this->type === self::TYPE_EQ ? $this->type
					: ($this->type === self::TYPE_GOE ? self::TYPE_LOE : self::TYPE_GOE);

			$this->limit = $this->limit->multiply(-1);

		} else {
			$set = $this->set;
			$type = $this->type;
		}

		$this->set = $set;
		$this->type = $type;

		return $this;
	}



	/** @return string */
	function getTypeSign()
	{
		return $this->type === self::TYPE_EQ ? '='
				: ($this->type === self::TYPE_LOE ? "\xe2\x89\xa4" : "\xe2\x89\xa5");
	}



	/** Deep copy */
	function __clone()
	{
		$this->limit = clone $this->limit;
	}

}
