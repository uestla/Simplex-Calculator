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

		$this->n = Math::mul($nn, $dd);
		$this->d = Math::mul($nd, $dn);

		$this->canonicalize();
	}


	/**
	 * @param  Fraction|numeric $a
	 * @return self
	 */
	public static function create($a)
	{
		if ($a instanceof self) {
			return clone $a;
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
		$this->n = Math::div($this->n, $gcd);
		$this->d = Math::div($this->d, $gcd);

		if (Math::comp($this->d, '0') === -1) {
			$this->n = Math::mul($this->n, '-1');
			$this->d = Math::mul($this->d, '-1');
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
			Math::add(
				Math::mul($this->n, $a->getDenominator()),
				Math::mul($this->d, $a->getNumerator())
			),
			Math::mul($this->d, $a->getDenominator())
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
			Math::mul($this->n, $a->getNumerator()),
			Math::mul($this->d, $a->getDenominator())
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
			Math::mul((string) $this->sgn(), $this->n),
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

		return Helpers::sgn(Math::sub(
			Math::mul($this->n, $a->getDenominator()),
			Math::mul($a->getNumerator(), $this->d)
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
			if ($expParts[1][0] === '-') { // negative exponent
				/** @var numeric-string $exp */
				$exp = substr($expParts[1], 1);

				$d = Math::pow('10', $exp);

			} else {
				/** @var numeric-string $exp */
				$exp = $expParts[1];

				$m = Math::pow('10', $exp);
			}
		}

		if (isset($dotParts[1])) {
			/** @var numeric-string $n */
			$n = implode('', $dotParts);
			$d = Math::pow('10', (string) strlen($dotParts[1]));

		} else {
			/** @var numeric-string $n */
			$n = $dotParts[0];
		}

		$n = Math::mul($n, $m);

		return array($n, $d);
	}

}
