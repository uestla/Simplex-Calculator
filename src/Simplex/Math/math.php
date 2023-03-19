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

namespace Simplex\Math;

use Simplex\ZeroGcdArgumentsException;


function add(string $a, string $b): string {
	return bcadd($a, $b, 0);
}


function sub(string $a, string $b): string {
	return bcsub($a, $b, 0);
}


function mul(string $a, string $b): string {
	if ($b === '-1') { // micro-optimization for multiplying by -1
		if ($a === '0') {
			return '0';
		}

		if (isNegative($a)) {
			return substr($a, 1);
		}

		return '-' . $a;
	}

	return bcmul($a, $b, 0);
}


function div(string $a, string $b, int $precision = 0): string {
	return bcdiv($a, $b, $precision);
}


function mod(string $a, string $b): string {
	return bcmod($a, $b, 0);
}


function abs(string $n): string {
	if (isNegative($n)) {
		return substr($n, 1);
	}

	return $n;
}


function isNegative(string $n): bool {
	return strncmp($n, '-', 1) === 0;
}


function comp(string $a, string $b): int {
	return bccomp($a, $b, 0);
}


function round(string $n, int $precision): string {
	if (isNegative($n)) {
		$result = bcsub($n, '0.' . str_repeat('0', $precision) . '5', $precision);

	} else {
		$result = bcadd($n, '0.' . str_repeat('0', $precision) . '5', $precision);
	}

	if (!str_contains($result, '.')) {
		return $result;
	}

	return rtrim(rtrim($result, '0'), '.');
}


function gcd(string $a, string $b): string {
	$aZero = $a === '0';
	$bZero = $b === '0';

	if ($aZero && $bZero) {
		throw new ZeroGcdArgumentsException;
	}

	if ($aZero) {
		return $b;
	}

	if ($bZero) {
		return $a;
	}

	while (true) {
		$mod = mod($a, $b);

		if ($mod === '0') {
			$gcd = $b;
			break;
		}

		$a = $b;
		$b = $mod;
	}

	return abs($gcd ?? $b);
}
