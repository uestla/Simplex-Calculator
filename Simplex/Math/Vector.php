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

use Simplex\EmptyVectorException;


final class Vector implements \Countable
{

	/** @var Fraction[] $values */
	private readonly array $values;

	private readonly int $size;


	/** @param  array<int, Fraction|string|int|float> $values */
	public function __construct(array $values)
	{
		if ($values === []) {
			throw new EmptyVectorException;
		}

		$this->values = array_map(static function ($value): Fraction {
			return Fraction::create($value);

		}, array_values($values));

		$this->size = count($this->values);
	}


	/** @param  Vector|array<int, Fraction|string|int|float> $values */
	public static function create(self|array $values): self
	{
		return $values instanceof self ? $values : new self($values);
	}


	/** @return Fraction[] */
	public function toArray(): array
	{
		return $this->values;
	}


	public function count(): int
	{
		return $this->size;
	}

}