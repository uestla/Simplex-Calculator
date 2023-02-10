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


final class Fraction
{

	/** @var int */
	private $n;

	/** @var int */
	private $d;


	/**
	 * @param  numeric $n
	 * @param  numeric $d
	 */
	public function __construct($n, $d = 1)
	{
		if (Helpers::isInt($n)) {
			$this->n = (int) $n;

		} else {
			$nf = self::fromDecimal($n);
			$this->n = $nf->getNumerator();
			$this->d = $nf->getDenominator();
		}

		if (Helpers::isInt($d)) {
			$this->d = (int) ($d * ($this->d === null ? 1 : $this->d));

		} else {
			$df = self::fromDecimal($d);
			$this->n *= $df->getDenominator();
			$this->d = ($df->getNumerator() * ($this->d === null ? 1 : $this->d));
		}

		$this->canonicalize();
	}


	/**
	 * @param  Fraction|numeric $a
	 * @return self
	 */
	public static function create($a)
	{
		if ($a instanceof self) {
			return $a;
		}

		return new self($a);
	}


	/** @return int */
	public function getNumerator()
	{
		return $this->n;
	}


	/** @return int */
	public function getDenominator()
	{
		return $this->d;
	}


	/** @return self */
	public function canonicalize()
	{
		if ($this->d === 0) {
			throw new \Exception('Division by zero.');
		}

		if ($this->d < 0) {
			$this->n = -$this->n;
			$this->d = -$this->d;
		}

		$gcd = Helpers::gcd($this->n, $this->d);
		$this->n /= $gcd;
		$this->d /= $gcd;
		return $this;
	}


	/**
	 * a/b + c/d = (ad + bc)/bd
	 *
	 * @param  Fraction|numeric $a
	 * @return self
	 */
	public function add($a)
	{
		$a = self::create($a);
		return new self($this->n * $a->getDenominator() + $this->d * $a->getNumerator(), $this->d * $a->getDenominator());
	}


	/**
	 * a/b - c/d = (ad - bc)/bd
	 *
	 * @param  Fraction|numeric $a
	 * @return self
	 */
	public function subtract($a)
	{
		return $this->add(self::create($a)->multiply(-1));
	}


	/**
	 * (a/b)(c/d) = (ac/bd)
	 *
	 * @param  Fraction|numeric $a
	 * @return self
	 */
	public function multiply($a)
	{
		$a = self::create($a);
		return new self($this->n * $a->getNumerator(), $this->d * $a->getDenominator());
	}


	/**
	 * (a/b)/(c/d) = ad/bc
	 *
	 * @param  Fraction|numeric $a
	 * @return self
	 */
	public function divide($a)
	{
		$a = self::create($a);
		return $this->multiply(new self($a->getDenominator(), $a->getNumerator()));
	}


	/** @return int -1, 0, 1 */
	public function sgn()
	{
		return Helpers::sgn($this->n);
	}


	/** @return self */
	public function absVal()
	{
		return new self($this->sgn() * $this->n, $this->d);
	}


	/**
	 * 2/3 vs 3/2 => 4/6 vs 9/6 => 4 < 9
	 *
	 * @param  Fraction|numeric $a
	 * @return int -1, 0, 1
	 */
	public function compare($a)
	{
		$a = self::create($a);
		return Helpers::sgn($this->n * $a->getDenominator() - $a->getNumerator() * $this->d);
	}


	/**
	 * @param  Fraction|numeric $a
	 * @return bool
	 */
	public function isEqualTo($a)
	{
		return $this->compare($a) === 0;
	}


	/**
	 * @param  Fraction|numeric $a
	 * @return bool
	 */
	public function isLowerThan($a)
	{
		return $this->compare($a) === -1;
	}


	/**
	 * @param  Fraction|numeric $a
	 * @return bool
	 */
	public function isGreaterThan($a)
	{
		return $this->compare($a) === 1;
	}


	/** @return string */
	public function toString()
	{
		return $this->n . ($this->d !== 1 ? '/' . $this->d : '');
	}


	/** @return float */
	public function toFloat()
	{
		return $this->n / $this->d;
	}


	/** @return string */
	public function __toString()
	{
		return $this->toString();
	}


	/**
	 * 0.25 => 25/100
	 *
	 * @param  float $n
	 * @return self
	 */
	private static function fromDecimal($n)
	{
		if (Helpers::isInt($n)) {
			return new self($n);
		}

		$decpart = ($n - (int) $n);
		$mlp = pow(10, strlen($decpart) - 2 - ($n < 0 ? 1 : 0));
		return new self((int) ($n * $mlp), $mlp);
	}

}
