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

	/** @var numeric-string */
	private $n;

	/** @var numeric-string */
	private $d;


	/**
	 * @param  numeric $n
	 * @param  numeric $d
	 */
	public function __construct($n, $d = '1')
	{
		list($nn, $nd) = self::factoryParts($n);
		list($dn, $dd) = self::factoryParts($d);

		$this->n = bcmul($nn, $dd);
		$this->d = bcmul($nd, $dn);

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


	/** @return numeric-string */
	public function getNumerator()
	{
		return $this->n;
	}


	/** @return numeric-string */
	public function getDenominator()
	{
		return $this->d;
	}


	/** @return self */
	public function canonicalize()
	{
		if ($this->d === '0') {
			throw new \Exception('Division by zero.');
		}

		$gcd = Helpers::gcd($this->n, $this->d);
		$this->n = bcdiv($this->n, $gcd);
		$this->d = bcdiv($this->d, $gcd);

		if (bccomp($this->d, '0') === -1) {
			$this->n = bcmul($this->n, '-1');
			$this->d = bcmul($this->d, '-1');
		}

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

		return new self(
			bcadd(
				bcmul($this->n, $a->getDenominator()),
				bcmul($this->d, $a->getNumerator())
			),
			bcmul($this->d, $a->getDenominator())
		);
	}


	/**
	 * a/b - c/d = (ad - bc)/bd
	 *
	 * @param  Fraction|numeric $a
	 * @return self
	 */
	public function subtract($a)
	{
		return $this->add(self::create($a)->multiply('-1'));
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

		return new self(
			bcmul($this->n, $a->getNumerator()),
			bcmul($this->d, $a->getDenominator())
		);
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
		return new self(
			bcmul($this->sgn(), $this->n),
			$this->d
		);
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

		return Helpers::sgn(bcsub(
			bcmul($this->n, $a->getDenominator()),
			bcmul($a->getNumerator(), $this->d)
		));
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


	/** @return string */
	public function toString()
	{
		return $this->n . ($this->d !== '1' ? '/' . $this->d : '');
	}


	/** @return string */
	public function __toString()
	{
		return $this->toString();
	}


	/**
	 * @param  Fraction|numeric $a
	 * @return bool
	 */
	public function isGreaterThan($a)
	{
		return $this->compare($a) === 1;
	}


	/** @return float */
	public function toFloat()
	{
		return $this->n / $this->d;
	}


	/**
	 * @param  numeric $a
	 * @return array{numeric-string, numeric-string}
	 */
	private function factoryParts($a)
	{
		if (!is_numeric($a)) {
			throw new \InvalidArgumentException(sprintf('Non-numeric argument "%s".', $a));
		}

		$d = '1';
		$m = '1';

		$expParts = explode('E', str_replace('e', 'E', (string) $a));
		$dotParts = explode('.', $expParts[0]);

		if (isset($expParts[1], $dotParts[1])) {
			throw new \InvalidArgumentException('Floats with scientific notation are not supported.');
		}

		if (isset($expParts[1])) {
			$m = bcpow('10', $expParts[1], 0);
		}

		if (isset($dotParts[1])) {
			$n = implode('', $dotParts);
			$d = bcpow('10', strlen($dotParts[1]), 0);

		} else {
			$n = $dotParts[0];
		}

		$n = bcmul($n, $m, 0);

		return array($n, $d);
	}

}
