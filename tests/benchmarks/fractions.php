<?php

declare(strict_types = 1);

namespace Simplex\Tests\Benchmarks;

use function Simplex\Math\abs;
use function Simplex\Math\add;
use function Simplex\Math\div;
use function Simplex\Math\gcd;
use function Simplex\Math\mul;
use function Simplex\Math\sub;
use function Simplex\Math\isNegative;


trait FractionBase
{
	public readonly string $n;
	public readonly string $d;


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


	public function multiply(self|string $a): self
	{
		if (is_string($a)) {
			return new self(
				mul($a, $this->n),
				$this->d,
			);
		}

		return new self(
			mul($this->n, $a->n),
			mul($this->d, $a->d),
		);
	}


	public function divide(self $a): self
	{
		return new self(
			mul($this->n, $a->d),
			mul($this->d, $a->n),
		);
	}


	public function abs(): self
	{
		return new self(abs($this->n), $this->d);
	}


	public function canonicalize(): self
	{
		$gcd = gcd($this->n, $this->d);
		$n = div($this->n, $gcd);
		$d = div($this->d, $gcd);

		if (isNegative($d)) {
			$n = mul($n, '-1');
			$d = mul($d, '-1');
		}

		return new self($n, $d);
	}
}


final class FractionCanonicalized
{
	use FractionBase;


	public function __construct(string $n, string $d = '1')
	{
		if ($d === '0') {
			throw new \RuntimeException('Division by zero.');
		}

		$gcd = gcd($n, $d);
		$n = div($n, $gcd);
		$d = div($d, $gcd);

		if (isNegative($d)) {
			$n = mul($n, '-1');
			$d = mul($d, '-1');
		}

		$this->n = $n;
		$this->d = $d;
	}


	public function isEqualTo(self|string $a): bool
	{
		if (is_string($a)) {
			return $this->d === '1' && $this->n === $a;
		}

		return $this->n === $a->n && $this->d === $a->d;
	}
}


final class FractionIsEqualMultiplication
{
	use FractionBase;


	public function __construct(string $n, string $d = '1')
	{
		if ($d === '0') {
			throw new \RuntimeException('Division by zero.');
		}

		$this->n = $n;
		$this->d = $d;
	}


	public function isEqualTo(self|string $a): bool
	{
		if (is_string($a)) {
			return $this->n === mul($a, $this->d);
		}

		return mul($this->n, $a->d) === mul($a->n, $this->d);
	}
}


final class FractionIsEqualCanonicalization
{
	use FractionBase;


	public function __construct(string $n, string $d = '1')
	{
		if ($d === '0') {
			throw new \RuntimeException('Division by zero.');
		}

		$this->n = $n;
		$this->d = $d;
	}


	public function isEqualTo(self|string $a): bool
	{
		$canThis = $this->canonicalize();

		if (is_string($a)) {
			return $canThis->d === '1' && $canThis->n === $a;
		}

		$canA = $a->canonicalize();
		return $canThis->n === $canA->n && $canThis->d === $canA->d;
	}
}
