<?php

declare(strict_types = 1);

namespace Simplex\Tests\Benchmarks;


final class NegateBC
{
	public static function negate(string $n): string
	{
		return bcmul($n, '-1', 0);
	}
}


final class NegateString
{
	public static function negate(string $n): string
	{
		if ($n === '0') {
			return '0';
		}

		if (strncmp($n, '-', 1) === 0) {
			return substr($n, 1);
		}

		return '-' . $n;
	}
}


function negateBC(string $n): string {
	return bcmul($n, '-1', 0);
}


function negateString(string $n): string {
	if ($n === '0') {
		return '0';
	}

	if ($n[0] === '-') {
		return substr($n, 1);
	}

	return '-' . $n;
}
