<?php

declare(strict_types = 1);

namespace Simplex\Tests;

use Tester\Assert;
use Tester\TestCase;
use function Simplex\Math\abs;
use function Simplex\Math\add;
use function Simplex\Math\div;
use function Simplex\Math\gcd;
use function Simplex\Math\mod;
use function Simplex\Math\mul;
use function Simplex\Math\sub;
use function Simplex\Math\comp;
use function Simplex\Math\round;
use function Simplex\Math\isNegative;
use Simplex\ZeroGcdArgumentsException;


require_once __DIR__ . '/bootstrap.php';


final class MathTest extends TestCase
{
	/** @dataProvider provideAddData */
	public function testAdd(string $a, string $b, string $expected): void
	{
		Assert::same($expected, add($a, $b));
	}


	/** @return array<array{string, string, string}> */
	public function provideAddData(): array
	{
		return [
			['0', '0', '0'],
			['1', '0', '1'],
			['-1', '0', '-1'],
			['1', '2', '3'],
			['-1', '-2', '-3'],
			['1', '-2', '-1'],
			['-1', '2', '1'],
			['9223372036854775807', '9223372036854775807', '18446744073709551614'],
		];
	}


	/** @dataProvider provideSubData */
	public function testSub(string $a, string $b, string $expected): void
	{
		Assert::same($expected, sub($a, $b));
	}


	/** @return array<array{string, string, string}> */
	public function provideSubData(): array
	{
		return [
			['0', '0', '0'],
			['1', '2', '-1'],
			['-1', '-2', '1'],
			['1', '-2', '3'],
			['-1', '2', '-3'],
			['9223372036854775807', '9223372036854775807', '0'],
			['9223372036854775807', '9223372036854775808', '-1'],
			['-9223372036854775807', '9223372036854775807', '-18446744073709551614'],
		];
	}


	/** @dataProvider provideMulData */
	public function testMul(string $a, string $b, string $expected): void
	{
		Assert::same($expected, mul($a, $b));
	}


	/** @return array<array{string, string, string}> */
	public function provideMulData(): array
	{
		return [
			['0', '0', '0'],
			['3', '0', '0'],
			['0', '3', '0'],
			['1', '3', '3'],
			['3', '1', '3'],
			['-1', '-3', '3'],
			['1', '-2', '-2'],
			['-1', '2', '-2'],
			['9223372036854775807', '9223372036854775807', '85070591730234615847396907784232501249'],
		];
	}


	/** @dataProvider provideDivData */
	public function testDiv(string $a, string $b, int $precision, string $expected): void
	{
		Assert::same($expected, div($a, $b, $precision));
	}


	/** @return array<array{string, string, int, string}> */
	public function provideDivData(): array
	{
		return [
			['0', '1', 0, '0'],
			['0', '1', 1, '0.0'],
			['0', '1', 2, '0.00'],

			['0', '-1', 0, '0'],
			['0', '-1', 1, '0.0'],
			['0', '-1', 2, '0.00'],

			['0', '2', 0, '0'],
			['0', '2', 1, '0.0'],
			['0', '2', 2, '0.00'],

			['0', '-2', 0, '0'],
			['0', '-2', 1, '0.0'],
			['0', '-2', 2, '0.00'],

			['1', '2', 0, '0'],
			['1', '2', 1, '0.5'],
			['1', '2', 2, '0.50'],

			['-1', '2', 0, '0'],
			['-1', '2', 1, '-0.5'],
			['-1', '2', 2, '-0.50'],

			['1', '-2', 0, '0'],
			['1', '-2', 1, '-0.5'],
			['1', '-2', 2, '-0.50'],

			['-1', '-2', 0, '0'],
			['-1', '-2', 1, '0.5'],
			['-1', '-2', 2, '0.50'],

			['2', '3', 0, '0'],
			['2', '3', 1, '0.6'],
			['2', '3', 2, '0.66'],
			['2', '3', 3, '0.666'],

			['1', '6', 0, '0'],
			['1', '6', 1, '0.1'],
			['1', '6', 2, '0.16'],
			['1', '6', 3, '0.166'],

			['6', '3', 0, '2'],
			['6', '3', 1, '2.0'],
			['6', '3', 2, '2.00'],

			['6', '4', 0, '1'],
			['6', '4', 1, '1.5'],
			['6', '4', 2, '1.50'],

			['18446744073709551614', '2', 0, '9223372036854775807'],
			['18446744073709551614', '2', 1, '9223372036854775807.0'],
			['18446744073709551614', '2', 2, '9223372036854775807.00'],
		];
	}


	/** @dataProvider provideDivisionByZeroData */
	public function testDivisionByZero(string $a, string $b, int $precision): void
	{
		Assert::error(static function () use ($a, $b, $precision): void {
			div($a, $b, $precision);

		}, \DivisionByZeroError::class);
	}


	/** @return array<array{string, string, int}> */
	public function provideDivisionByZeroData(): array
	{
		return [
			['', '', 0],
			['', '', 1],
			['', '', 2],

			['1', '', 0],
			['1', '', 1],
			['1', '', 2],

			['-1', '', 0],
			['-1', '', 1],
			['-1', '', 2],

			['0', '0', 0],
			['0', '0', 1],
			['0', '0', 2],

			['1', '0', 0],
			['1', '0', 1],
			['1', '0', 2],

			['-1', '0', 0],
			['-1', '0', 1],
			['-1', '0', 2],

			['9223372036854775807', '0', 0],
			['9223372036854775807', '0', 1],
			['9223372036854775807', '0', 2],

			['9223372036854775807', '0.0', 0],
			['9223372036854775807', '0.0', 1],
			['9223372036854775807', '0.0', 2],

			['9223372036854775807', '0.00', 0],
			['9223372036854775807', '0.00', 1],
			['9223372036854775807', '0.00', 2],

			['-9223372036854775807', '0', 0],
			['-9223372036854775807', '0', 1],
			['-9223372036854775807', '0', 2],
		];
	}


	/** @dataProvider provideModData */
	public function testMod(string $a, string $b, string $expected): void
	{
		Assert::same($expected, mod($a, $b));
	}


	/** @return array<array{string, string, string}> */
	public function provideModData(): array
	{
		return [
			['0', '1', '0'],
			['0', '2', '0'],
			['0', '3', '0'],
			['0', '4', '0'],
			['0', '5', '0'],

			['1', '1', '0'],
			['1', '2', '1'],
			['1', '3', '1'],
			['1', '4', '1'],
			['1', '5', '1'],

			['4', '3', '1'],
			['4', '2', '0'],
			['4', '1', '0'],

			['-4', '3', '-1'],
			['-4', '2', '0'],
			['-4', '1', '0'],

			['4', '-3', '1'],
			['4', '-2', '0'],
			['4', '-1', '0'],

			['5', '4', '1'],
			['5', '3', '2'],
			['5', '2', '1'],
			['5', '1', '0'],
		];
	}


	/** @dataProvider provideAbsData */
	public function testAbs(string $n, string $expected): void
	{
		Assert::same($expected, abs($n));
	}


	/** @return array<array{string, string}> */
	public function provideAbsData(): array
	{
		return [
			['', ''],
			['0', '0'],
			['1', '1'],
			['2', '2'],
			['3', '3'],
			['4', '4'],
			['5', '5'],
			['-1', '1'],
			['-2', '2'],
			['-3', '3'],
			['-4', '4'],
			['-5', '5'],
		];
	}


	/** @dataProvider provideIsNegativeData */
	public function testIsNegative(string $n, bool $expected): void
	{
		Assert::same($expected, isNegative($n));
	}


	/** @return array<array{string, bool}> */
	public function provideIsNegativeData(): array
	{
		return [
			['', false],
			['0', false],
			['1', false],
			['2', false],
			['3', false],
			['-1', true],
			['-2', true],
			['-3', true],
		];
	}


	/** @dataProvider provideCompData */
	public function testComp(string $a, string $b, int $expected): void
	{
		Assert::same($expected, comp($a, $b));
	}


	/** @return array<array{string, string, int}> */
	public function provideCompData(): array
	{
		return [
			['0', '0', 0],
			['1', '1', 0],
			['-1', '-1', 0],
			['1', '-1', 1],
			['-1', '1', -1],
			['-2', '-1', -1],
			['-1', '-2', 1],
			['1', '2', -1],
			['2', '1', 1],
		];
	}


	/** @dataProvider provideRoundData */
	public function testRound(string $a, int $precision, string $expected): void
	{
		Assert::same($expected, round($a, $precision));
	}


	/** @return array<array{string, int, string}> */
	public function provideRoundData(): array
	{
		return [
			['0', 0, '0'],
			['0', 1, '0'],
			['0', 2, '0'],
			['0', 3, '0'],
			['0', 4, '0'],
			['0', 5, '0'],

			['1.1', 0, '1'],
			['1.1', 1, '1.1'],
			['1.1', 2, '1.1'],

			['-1.1', 0, '-1'],
			['-1.1', 1, '-1.1'],
			['-1.1', 2, '-1.1'],

			['1.3', 0, '1'],
			['1.3', 1, '1.3'],
			['1.3', 2, '1.3'],

			['-1.3', 0, '-1'],
			['-1.3', 1, '-1.3'],
			['-1.3', 2, '-1.3'],

			['1.5', 0, '2'],
			['1.5', 1, '1.5'],
			['1.5', 2, '1.5'],

			['-1.5', 0, '-2'],
			['-1.5', 1, '-1.5'],
			['-1.5', 2, '-1.5'],

			['2.33', 0, '2'],
			['2.33', 1, '2.3'],
			['2.33', 2, '2.33'],
			['2.33', 3, '2.33'],

			['-2.33', 0, '-2'],
			['-2.33', 1, '-2.3'],
			['-2.33', 2, '-2.33'],
			['-2.33', 3, '-2.33'],

			['3.45', 0, '3'],
			['3.45', 1, '3.5'],
			['3.45', 2, '3.45'],
			['3.45', 3, '3.45'],

			['-3.45', 0, '-3'],
			['-3.45', 1, '-3.5'],
			['-3.45', 2, '-3.45'],
			['-3.45', 3, '-3.45'],
		];
	}


	/** @dataProvider provideGcdData */
	public function testGcd(string $a, string $b, string $expected): void
	{
		Assert::same($expected, gcd($a, $b));
	}


	/** @return array<array{string, string, string}> */
	public function provideGcdData(): array
	{
		return [
			['0', '21', '21'],
			['21', '0', '21'],

			['1', '24', '1'],
			['24', '1', '1'],

			['6', '27', '3'],
			['-6', '27', '3'],
			['6', '-27', '3'],
			['-6', '-27', '3'],

			['1400000000000000256', '100000000000000000', '256'],
		];
	}


	public function testGcdZeroArguments(): void
	{
		Assert::exception(static function () {
			gcd('0', '0');

		}, ZeroGcdArgumentsException::class, 'At least one number must not be a zero.');
	}
}


(new MathTest)->run();
