<?php

declare(strict_types = 1);

/**
 * This file is part of the SimplexCalculator library
 *
 * Copyright (c) 2014 Petr Kessler (https://kesspess.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Simplex-Calculator
 */

namespace Simplex\Math;

use Simplex\DivisionByZeroException;
use Simplex\ScientificFloatException;
use Simplex\NonNumericArgumentException;


final class Fraction implements \Stringable
{

	private readonly string $n;
	private readonly string $d;


	public function __construct(string $n, string $d = '1')
	{
		if ($d === '0') {
			throw new DivisionByZeroException;
		}

		if ($d === '-1') {
			$n = mul($n, '-1');
			$d = '1';

		} elseif ($d !== '1') {
			$gcd = gcd($n, $d);

			if ($gcd !== '1') {
				$n = div($n, $gcd);
				$d = div($d, $gcd);
			}

			if (isNegative($d)) {
				$n = mul($n, '-1');
				$d = mul($d, '-1');
			}
		}

		$this->n = $n;
		$this->d = $d;
	}


	public static function create(self|string|int|float $a): self
	{
		if ($a instanceof self) {
			return $a;
		}

		[$n, $d] = self::factoryParts($a);
		return new self($n, $d);
	}


	public function getNumerator(): string
	{
		return $this->n;
	}


	public function getDenominator(): string
	{
		return $this->d;
	}


	/** a/b + c/d = (ad + bc)/bd */
	public function add(self $a): self
	{
		return new self(
			add(
				mul($this->n, $a->d),
				mul($this->d, $a->n),
			),
			mul($this->d, $a->d),
		);
	}


	/** a/b - c/d = (ad - bc)/bd */
	public function subtract(self $a): self
	{
		return new self(
			sub(
				mul($this->n, $a->d),
				mul($this->d, $a->n),
			),
			mul($this->d, $a->d),
		);
	}


	/** (a/b)(c/d) = (ac/bd) */
	public function multiply(self|string $a): self
	{
		if (is_string($a)) {
			return new self(
				mul($this->n, $a),
				$this->d,
			);
		}

		return new self(
			mul($this->n, $a->n),
			mul($this->d, $a->d),
		);
	}


	/** (a/b)/(c/d) = ad/bc */
	public function divide(self $a): self
	{
		return new self(
			mul($this->n, $a->d),
			mul($this->d, $a->n),
		);
	}


	public function abs(): self
	{
		return new self(
			abs($this->n),
			$this->d,
		);
	}


	public function isEqualTo(self|string $a): bool
	{
		if (is_string($a)) {
			return $this->d === '1' && $this->n === $a;
		}

		return $this->n === $a->n && $this->d === $a->d;
	}


	public function isPositive(): bool
	{
		return $this->n !== '0' && !$this->isNegative();
	}


	public function isNegative(): bool
	{
		return isNegative($this->n);
	}


	public function isLowerThan(Fraction $a): bool
	{
		return comp(
			mul($this->n, $a->getDenominator()),
			mul($a->getNumerator(), $this->d),

		) === -1;
	}


	public function toNumericString(int $precision): string
	{
		return round(div($this->n, $this->d, $precision + 1), $precision);
	}


	public function toString(): string
	{
		return $this->n . ($this->d === '1' ? '' : '/' . $this->d);
	}


	public function __toString(): string
	{
		return $this->toString();
	}


	/** @return array{string, string} */
	private static function factoryParts(string|int|float $a): array
	{
		if (!is_numeric($a)) {
			throw new NonNumericArgumentException($a);
		}

		$n = (string) $a;
		$d = '1';

		$expPos = stripos($n, 'e'); // exponent
		$dotPos = strpos($n, '.'); // decimal point

		if ($expPos !== false && $dotPos !== false) {
			throw new ScientificFloatException;
		}

		if ($expPos !== false) {
			$exp = substr($n, $expPos + 1);
			$n = substr($n, 0, $expPos);

			if ($exp[0] === '-') { // negative exponent
				$d = pow('10', substr($exp, 1));

			} else {
				$n = mul($n, pow('10', $exp));
			}

		} elseif ($dotPos !== false) {
			$d = pow('10', (string) (strlen($n) - $dotPos - 1));
			$n = ltrim(str_replace('.', '', $n), '0');
		}

		return [$n, $d];
	}

}