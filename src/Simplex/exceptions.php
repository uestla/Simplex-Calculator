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


abstract class SimplexException extends \Exception
{}
