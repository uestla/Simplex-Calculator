<?php

declare(strict_types = 1);

namespace Simplex\Tests;

use Tester\Assert;
use Tester\TestCase;
use Simplex\Math\Fraction;
use Simplex\DivisionByZeroException;
use Simplex\ScientificFloatException;
use Simplex\NonNumericArgumentException;


require_once __DIR__ . '/bootstrap.php';


final class FractionTest extends TestCase
{
	/** @dataProvider provideConstructorData */
	public function testConstructor(string $n, string $d, string $expectedN, string $expectedD): void
	{
		$f = new Fraction($n, $d);
		Assert::same($expectedN, $f->n);
		Assert::same($expectedD, $f->d);
	}


	/** @return array<array{string, string, string, string}> */
	public function provideConstructorData(): array
	{
		return [
			['1', '1', '1', '1'],
			['01', '1', '1', '1'],
			['001', '1', '1', '1'],
			['-001', '1', '-1', '1'],
			['1', '-1', '-1', '1'],
			['0', '42', '0', '1'],
			['10', '2', '5', '1'],
			['-10', '2', '-5', '1'],
			['10', '-2', '-5', '1'],
			['-10', '-2', '5', '1'],
		];
	}


	/** @dataProvider provideNonNumericArgumentData */
	public function testNonNumericArgument(string $s, bool $testCreate): void
	{
		Assert::exception(static function () use ($s): void {
			new Fraction($s);

		}, NonNumericArgumentException::class, sprintf('Non-numeric argument "%s".', $s));

		Assert::exception(static function () use ($s): void {
			new Fraction('1', $s);

		}, NonNumericArgumentException::class, sprintf('Non-numeric argument "%s".', $s));

		Assert::exception(static function () use ($s): void {
			(new Fraction('1'))->isEqualTo($s);

		}, NonNumericArgumentException::class, sprintf('Non-numeric argument "%s".', $s));

		Assert::exception(static function () use ($s): void {
			(new Fraction('1'))->multiply($s);

		}, NonNumericArgumentException::class, sprintf('Non-numeric argument "%s".', $s));

		if ($testCreate) {
			Assert::exception(static function () use ($s): void {
				Fraction::create($s);

			}, NonNumericArgumentException::class, sprintf('Non-numeric argument "%s".', $s));
		}
	}


	/** @return array<array{string, bool}> */
	public function provideNonNumericArgumentData(): array
	{
		return [
			['asdf', true],
			['', true],
			['0.0.1', true],
			['-', true],
			['1/2', true],
			['-1/2', true],
			['1/-2', true],
			['-1/-2', true],

			['2e14', false],
			['0.3e14', false],
			['-0.3e-14', false],
		];
	}


	/** @dataProvider provideConstructorDivisionByZeroData */
	public function testConstructorDivisionByZero(string $n, string $d): void
	{
		Assert::exception(static function () use ($n, $d) {
			new Fraction($n, $d);

		}, DivisionByZeroException::class, 'Division by zero.');
	}


	/** @return array<array{string, string}> */
	public function provideConstructorDivisionByZeroData(): array
	{
		return [
			['42', '0'],
			['-42', '0'],
			['0', '0'],
		];
	}


	/** @dataProvider provideCreateData */
	public function testCreate(Fraction|string|int|float $a, string $expectedN, string $expectedD): void
	{
		$f = Fraction::create($a);
		Assert::same($expectedN, $f->n);
		Assert::same($expectedD, $f->d);
	}


	/** @return array<array{Fraction|string|int|float, string, string}> */
	public function provideCreateData(): array
	{
		return [
			// fraction
			[new Fraction('2', '3'), '2', '3'],
			[new Fraction('20', '30'), '2', '3'],

			// integer
			[42, '42', '1'],
			[-42, '-42', '1'],
			[0, '0', '1'],

			// float
			[42.0, '42', '1'],
			[-42.0, '-42', '1'],
			[0.0, '0', '1'],
			[42.00, '42', '1'],
			[42.5, '85', '2'],
			[0.25, '1', '4'],
			[-0.25, '-1', '4'],
			[0.14, '7', '50'],
			[-0.14, '-7', '50'],
			[3.5e3, '3500', '1'],

			// string
			['42', '42', '1'],
			['-42', '-42', '1'],
			['0', '0', '1'],
			['0.0', '0', '1'],
			['42.0', '42', '1'],
			['-42.0', '-42', '1'],
			['42.00', '42', '1'],
			['-42.00', '-42', '1'],
			['42.5', '85', '2'],
			['-42.5', '-85', '2'],
			['42e3', '42000', '1'],
			['42e+3', '42000', '1'],
			['-42e3', '-42000', '1'],
			['-42e+3', '-42000', '1'],
			['42E3', '42000', '1'],
			['42E+3', '42000', '1'],
			['-42E3', '-42000', '1'],
			['-42E+3', '-42000', '1'],
			['42e-3', '21', '500'],
			['-42e-3', '-21', '500'],
		];
	}


	/** @dataProvider provideFloatsScientificNotationData */
	public function testFloatsScientificNotation(float|string $a): void
	{
		Assert::exception(static function () use ($a) {
			Fraction::create($a);

		}, ScientificFloatException::class, 'Floats with scientific notation are not supported.');
	}


	/** @return array<array{float|numeric-string}> */
	public function provideFloatsScientificNotationData(): array
	{
		return [
			[PHP_INT_MAX + 1],
			[-PHP_INT_MAX - 2],
			['-1.23e50'],
			['-1.23E50'],
			['1.23e50'],
			['1.23E50'],
		];
	}


	/** @dataProvider provideAddData */
	public function testAdd(Fraction $a, Fraction $b, string $expectedN, string $expectedD): void
	{
		$result = $a->add($b);
		Assert::same($expectedN, $result->n);
		Assert::same($expectedD, $result->d);

		$result = $b->add($a);
		Assert::same($expectedN, $result->n);
		Assert::same($expectedD, $result->d);
	}


	/** @return array<array{Fraction, Fraction, string, string}> */
	public function provideAddData(): array
	{
		return [
			[new Fraction('42'), new Fraction('0'), '42', '1'],

			[new Fraction('1'), new Fraction('2'), '3', '1'],
			[new Fraction('-1'), new Fraction('2'), '1', '1'],
			[new Fraction('1'), new Fraction('-2'), '-1', '1'],
			[new Fraction('-1'), new Fraction('-2'), '-3', '1'],
			[new Fraction('-1'), new Fraction('1'), '0', '1'],

			[new Fraction('1', '4'), new Fraction('2', '3'), '11', '12'],
			[new Fraction('1', '4'), new Fraction('-2', '3'), '-5', '12'],
			[new Fraction('-1', '4'), new Fraction('2', '3'), '5', '12'],
			[new Fraction('-1', '4'), new Fraction('-2', '3'), '-11', '12'],
		];
	}


	/** @dataProvider provideSubtractData */
	public function testSubtract(Fraction $a, Fraction $b, string $expectedN, string $expectedD): void
	{
		$result = $a->subtract($b);
		Assert::same($expectedN, $result->n);
		Assert::same($expectedD, $result->d);
	}


	/** @return array<array{Fraction, Fraction, string, string}> */
	public function provideSubtractData(): array
	{
		return [
			[new Fraction('42'), new Fraction('0'), '42', '1'],

			[new Fraction('1'), new Fraction('2'), '-1', '1'],
			[new Fraction('-1'), new Fraction('2'), '-3', '1'],
			[new Fraction('1'), new Fraction('-2'), '3', '1'],
			[new Fraction('-1'), new Fraction('-2'), '1', '1'],
			[new Fraction('1'), new Fraction('1'), '0', '1'],

			[new Fraction('1', '4'), new Fraction('2', '3'), '-5', '12'],
			[new Fraction('1', '4'), new Fraction('-2', '3'), '11', '12'],
			[new Fraction('-1', '4'), new Fraction('2', '3'), '-11', '12'],
			[new Fraction('-1', '4'), new Fraction('-2', '3'), '5', '12'],
		];
	}


	/** @dataProvider provideMultiplyData */
	public function testMultiply(Fraction $a, Fraction|string $b, string $expectedN, string $expectedD): void
	{
		$result = $a->multiply($b);
		Assert::same($expectedN, $result->n);
		Assert::same($expectedD, $result->d);

		if ($b instanceof Fraction) {
			$result = $b->multiply($a);
			Assert::same($expectedN, $result->n);
			Assert::same($expectedD, $result->d);
		}
	}


	/** @return array<array{Fraction, Fraction|string, string, string}> */
	public function provideMultiplyData(): array
	{
		return [
			[new Fraction('42'), new Fraction('0'), '0', '1'],
			[new Fraction('42'), '0', '0', '1'],
			[new Fraction('42'), '00', '0', '1'],
			[new Fraction('42'), new Fraction('1'), '42', '1'],
			[new Fraction('42'), '1', '42', '1'],
			[new Fraction('42'), '01', '42', '1'],
			[new Fraction('42', '13'), new Fraction('0'), '0', '1'],
			[new Fraction('42', '13'), '0', '0', '1'],
			[new Fraction('-42'), new Fraction('0'), '0', '1'],
			[new Fraction('-42'), '0', '0', '1'],
			[new Fraction('-42'), '1', '-42', '1'],
			[new Fraction('-42'), new Fraction('1'), '-42', '1'],
			[new Fraction('-42', '13'), new Fraction('0'), '0', '1'],
			[new Fraction('-42', '13'), '0', '0', '1'],

			[new Fraction('0'), '-1', '0', '1'],
			[new Fraction('42'), '-1', '-42', '1'],
			[new Fraction('42'), '-01', '-42', '1'],
			[new Fraction('42'), '-001', '-42', '1'],
			[new Fraction('42', '13'), '-1', '-42', '13'],
			[new Fraction('-42', '13'), '-1', '42', '13'],

			[new Fraction('1', '4'), '2', '1', '2'],
			[new Fraction('-1', '4'), '2', '-1', '2'],
			[new Fraction('1', '4'), '-2', '-1', '2'],
			[new Fraction('-1', '4'), '-2', '1', '2'],

			[new Fraction('1', '4'), new Fraction('2', '3'), '1', '6'],
			[new Fraction('-1', '4'), new Fraction('2', '3'), '-1', '6'],
			[new Fraction('1', '4'), new Fraction('-2', '3'), '-1', '6'],
			[new Fraction('-1', '4'), new Fraction('-2', '3'), '1', '6'],
		];
	}


	/** @dataProvider provideDivideData */
	public function testDivide(Fraction $a, Fraction $b, string $expectedN, string $expectedD): void
	{
		$result = $a->divide($b);
		Assert::same($expectedN, $result->n);
		Assert::same($expectedD, $result->d);
	}


	/** @return array<array{Fraction, Fraction|string, string, string}> */
	public function provideDivideData(): array
	{
		return [
			[new Fraction('0'), new Fraction('42'), '0', '1'],
			[new Fraction('0'), new Fraction('-42'), '0', '1'],
			[new Fraction('0'), new Fraction('42', '13'), '0', '1'],
			[new Fraction('0'), new Fraction('-42', '13'), '0', '1'],

			[new Fraction('1', '4'), new Fraction('2', '3'), '3', '8'],
			[new Fraction('-1', '4'), new Fraction('2', '3'), '-3', '8'],
			[new Fraction('1', '4'), new Fraction('-2', '3'), '-3', '8'],
			[new Fraction('-1', '4'), new Fraction('-2', '3'), '3', '8'],
		];
	}


	/** @dataProvider provideDivideDivisionByZeroData */
	public function testDivideDivisionByZero(Fraction $a, Fraction $b): void
	{
		Assert::exception(static function () use ($a, $b) {
			$a->divide($b);

		}, DivisionByZeroException::class, 'Division by zero.');
	}


	/** @return array<array{Fraction, Fraction}> */
	public function provideDivideDivisionByZeroData(): array
	{
		return [
			[new Fraction('0'), new Fraction('0')],
			[new Fraction('42'), new Fraction('0')],
			[new Fraction('-42'), new Fraction('0')],
			[new Fraction('42', '13'), new Fraction('0')],
			[new Fraction('-42', '13'), new Fraction('0')],
		];
	}


	/** @dataProvider provideAbsData */
	public function testAbs(Fraction $f, string $expectedN, string $expectedD): void
	{
		$result = $f->abs();
		Assert::same($expectedN, $result->n);
		Assert::same($expectedD, $result->d);
	}


	/** @return array<array{Fraction, string, string}> */
	public function provideAbsData(): array
	{
		return [
			[new Fraction('0'), '0', '1'],
			[new Fraction('42'), '42', '1'],
			[new Fraction('-42'), '42', '1'],
			[new Fraction('42', '13'), '42', '13'],
			[new Fraction('-42', '13'), '42', '13'],
		];
	}


	/** @dataProvider provideIsEqualToData */
	public function testIsEqualTo(Fraction $a, Fraction|string $b, bool $expected): void
	{
		Assert::same($expected, $a->isEqualTo($b));

		if ($b instanceof Fraction) {
			Assert::same($expected, $b->isEqualTo($a));
		}
	}


	/** @return array<array{Fraction, Fraction|string, bool}> */
	public function provideIsEqualToData(): array
	{
		return [
			[new Fraction('0'), new Fraction('0'), true],
			[new Fraction('0'), new Fraction('1'), false],

			[new Fraction('0'), '0', true],
			[new Fraction('0'), '00', true],
			[new Fraction('0'), '1', false],
			[new Fraction('0'), '01', false],
			[new Fraction('0'), '001', false],

			[new Fraction('1'), '1', true],
			[new Fraction('-1'), '-1', true],
			[new Fraction('1'), '001', true],
			[new Fraction('-1'), '-01', true],
			[new Fraction('-1'), '-001', true],

			[new Fraction('0', '1'), new Fraction('0', '2'), true],

			[new Fraction('42'), new Fraction('84', '2'), true],
			[new Fraction('-42'), new Fraction('-84', '2'), true],

			[new Fraction('126', '3'), new Fraction('84', '2'), true],
			[new Fraction('-126', '3'), new Fraction('-84', '2'), true],

			[new Fraction('1', '3'), new Fraction('2', '3'), false],
			[new Fraction('1', '3'), new Fraction('1', '2'), false],
		];
	}


	/** @dataProvider provideIsPositiveIsNegativeData */
	public function testIsPositiveIsNegative(Fraction $f, bool $expectedPositive, bool $expectedNegative): void
	{
		Assert::same($expectedPositive, $f->isPositive());
		Assert::same($expectedNegative, $f->isNegative());
	}


	/** @return array<array{Fraction, bool, bool}> */
	public function provideIsPositiveIsNegativeData(): array
	{
		return [
			[new Fraction('0'), false, false],
			[new Fraction('1'), true, false],
			[new Fraction('-1'), false, true],
			[new Fraction('-1', '2'), false, true],
			[new Fraction('1', '-2'), false, true],
			[new Fraction('-1', '-2'), true, false],
		];
	}


	/** @dataProvider provideIsLowerThanData */
	public function testIsLowerThan(Fraction $a, Fraction $b): void
	{
		Assert::true($a->isLowerThan($b));
		Assert::false($b->isLowerThan($a));
		Assert::false($a->isEqualTo($b));
	}


	/** @return array<array{Fraction, Fraction}> */
	public function provideIsLowerThanData(): array
	{
		return [
			[new Fraction('0'), new Fraction('1')],
			[new Fraction('-1'), new Fraction('0')],
			[new Fraction('1', '2'), new Fraction('2', '3')],
			[new Fraction('-2', '3'), new Fraction('-1', '2')],
			[new Fraction('-1', '-2'), new Fraction('-2', '-3')],
		];
	}


	/** @dataProvider provideToNumericStringData */
	public function testToNumericString(Fraction $a, int $precision, string $expected): void
	{
		Assert::same($expected, $a->toNumericString($precision));
	}


	/** @return array<array{Fraction, int, string}> */
	public function provideToNumericStringData(): array
	{
		return [
			[new Fraction('42'), 0, '42'],
			[new Fraction('42'), 1, '42'],
			[new Fraction('42'), 2, '42'],
			[new Fraction('42'), 3, '42'],
			[new Fraction('42'), 4, '42'],

			[new Fraction('-42'), 0, '-42'],
			[new Fraction('-42'), 1, '-42'],
			[new Fraction('-42'), 2, '-42'],
			[new Fraction('-42'), 3, '-42'],
			[new Fraction('-42'), 4, '-42'],

			[new Fraction('1', '3'), 0, '0'],
			[new Fraction('1', '3'), 1, '0.3'],
			[new Fraction('1', '3'), 2, '0.33'],
			[new Fraction('1', '3'), 3, '0.333'],
			[new Fraction('1', '3'), 4, '0.3333'],

			[new Fraction('-1', '3'), 0, '0'],
			[new Fraction('-1', '3'), 1, '-0.3'],
			[new Fraction('-1', '3'), 2, '-0.33'],
			[new Fraction('-1', '3'), 3, '-0.333'],
			[new Fraction('-1', '3'), 4, '-0.3333'],

			[new Fraction('13', '6'), 0, '2'],
			[new Fraction('13', '6'), 1, '2.2'],
			[new Fraction('13', '6'), 2, '2.17'],
			[new Fraction('13', '6'), 3, '2.167'],
			[new Fraction('13', '6'), 4, '2.1667'],

			[new Fraction('-13', '6'), 0, '-2'],
			[new Fraction('-13', '6'), 1, '-2.2'],
			[new Fraction('-13', '6'), 2, '-2.17'],
			[new Fraction('-13', '6'), 3, '-2.167'],
			[new Fraction('-13', '6'), 4, '-2.1667'],
		];
	}


	/** @dataProvider provideToStringData */
	public function testToString(Fraction $f, string $expected): void
	{
		Assert::same($expected, $f->toString());
		Assert::same($expected, (string) $f);
	}


	/** @return array<array{Fraction, string}> */
	public function provideToStringData(): array
	{
		return [
			[new Fraction('0'), '0'],
			[new Fraction('1'), '1'],
			[new Fraction('-1'), '-1'],
			[new Fraction('1', '2'), '1/2'],
			[new Fraction('2', '4'), '1/2'],
		];
	}
}


(new FractionTest)->run();
