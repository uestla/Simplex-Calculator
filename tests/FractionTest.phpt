<?php

namespace Simplex\Tests;

use Tester\Assert;
use Tester\TestCase;
use Simplex\Fraction;

require_once __DIR__ . '/bootstrap.php';


final class FractionTest extends TestCase
{

	/** @return void */
	public function testScientificNotation()
	{
		Assert::same('1000', (string) new Fraction('1e3'));
		Assert::same('1000', (string) new Fraction('1E3'));
		Assert::same('1000', (string) new Fraction('1E+3'));
		Assert::same('1000', (string) new Fraction('1E+4', '10'));
	}


	/** @return void */
	public function testDivisionByZero()
	{
		Assert::exception(function () {
			new Fraction(3, 0);

		}, 'Exception', 'Division by zero.');
	}


	/** @return void */
	public function testFloatConversion1()
	{
		Assert::same('1/4', (string) new Fraction(0.25));
	}


	/** @return void */
	public function testFloatConversion2()
	{
		Assert::same('7/50', (string) new Fraction(0.14));
	}


	/** @return void */
	public function testNegativeDecimalCanonicalization()
	{
		$f = new Fraction(-0.25);
		Assert::same('-1/4', (string) $f);
		Assert::notSame('1/-4', (string) $f);
	}


	/** @return void */
	public function testNegativeNumeratorAndDenominatorCanonicalization()
	{
		Assert::same('1/4', (string) new Fraction(-0.25, -1));
	}


	/** @return void */
	public function testDecimalDenominatorCanonicalization()
	{
		Assert::same('32', (string) new Fraction(8, 1/4));
	}


	/** @return void */
	public function testAddition()
	{
		$a = new Fraction(1, 4);
		$b = new Fraction(2, 3);

		Assert::same('11/12', (string) $a->add($b));
		Assert::same('11/12', (string) $b->add($a));
	}


	/** @return void */
	public function testMultiplication()
	{
		$a = new Fraction(1, 4);
		$b = new Fraction(2, 3);

		Assert::same('1/6', (string) $a->multiply($b));
		Assert::same('1/6', (string) $b->multiply($a));
	}


	/** @return void */
	public function testNegation()
	{
		$a = new Fraction(1/4);

		Assert::same('-1/4', (string) $a->multiply(-1));
	}


	/** @return void */
	public function testDivision()
	{
		$a = new Fraction(1, 4);
		$b = new Fraction(2, 3);

		Assert::same('3/8', (string) $a->divide($b));
	}


	/** @return void */
	public function testSgnAndAbs()
	{
		$a = new Fraction(-1/4);

		Assert::same(-1, $a->sgn());
		Assert::same('1/4', (string) $a->absVal());
	}


	/** @return void */
	public function testComparison()
	{
		$a = new Fraction(2, 3);
		$b = new Fraction(3, 2);

		Assert::true($a->isLowerThan($b));
		Assert::true($b->isGreaterThan($a));
		Assert::false($a->isLowerThan($a));
		Assert::false($a->isGreaterThan($a));
	}


	/**
	 * @dataProvider provideFloatsScientificNotation
	 * @param  scalar $i
	 * @return void
	 */
	public function testFloatsScientificNotation($i)
	{
		Assert::exception(function () use ($i) {
			new Fraction($i);

		}, 'InvalidArgumentException', 'Floats with scientific notation are not supported.');
	}


	/**
	 * @dataProvider provideNonNumericArguments
	 * @param  scalar $a
	 * @return void
	 */
	public function testNonNumericArguments($a)
	{
		Assert::exception(function () use ($a) {
			new Fraction($a);

		}, 'InvalidArgumentException', sprintf('Non-numeric argument "%s".', $a));
	}


	// === data providers =====================================================

	/** @return array<int, array<int, scalar|null>> */
	public function provideNonNumericArguments()
	{
		return array(
			array('ASDF'),
			array('1234EF+20'),
			array('-14BFL'),
			array(''),
			array(null),
			array(true),
			array(false),
		);
	}


	/** @return array<int, scalar[]> */
	public function provideFloatsScientificNotation()
	{
		return array(
			array(PHP_INT_MAX + 1),
			array(- PHP_INT_MAX - 2),
			array('-1.23e50'),
			array('-1.23E50'),
		);
	}

}


id(new FractionTest)->run();
