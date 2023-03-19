<?php

declare(strict_types = 1);

/**
 * This file is part of the Simplex-Calculator library
 *
 * Copyright (c) 2014 Petr Kessler (https://kesspess.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Simplex-Calculator
 */

namespace Simplex;


final class ZeroGcdArgumentsException extends SimplexException
{
	public function __construct()
	{
		parent::__construct('At least one number must not be a zero.');
	}
}


final class DivisionByZeroException extends SimplexException
{
	public function __construct()
	{
		parent::__construct('Division by zero.');
	}
}


final class NonNumericArgumentException extends SimplexException
{
	public function __construct(string $s)
	{
		parent::__construct(sprintf('Non-numeric argument "%s".', $s));
	}
}


final class ScientificFloatException extends SimplexException
{
	public function __construct()
	{
		parent::__construct('Floats with scientific notation are not supported.');
	}
}


abstract class SimplexException extends \Exception
{}
