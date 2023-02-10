<?php

namespace Simplex\Tests;

use Tester\Assert;
use Tester\TestCase;
use Simplex\Fraction;

require_once __DIR__ . '/bootstrap.php';


final class FractionTest extends TestCase
{

	public function testDivisionByZero()
	{
		Assert::exception(function () {
			new Fraction(3, 0);

		}, 'Exception', 'Division by zero.');
	}


	public function testFloatConversion1()
	{
		Assert::same('1/4', (string) new Fraction(0.25));
	}


	public function testFloatConversion2()
	{
		Assert::same('7/50', (string) new Fraction(0.14));
	}


	public function testNegativeDecimalCanonicalization()
	{
		$f = new Fraction(-0.25);
		Assert::same('-1/4', (string) $f);
		Assert::notSame('1/-4', (string) $f);
	}


	public function testNegativeNumeratorAndDenominatorCanonicalization()
	{
		Assert::same('1/4', (string) new Fraction(-0.25, -1));
	}


	public function testDecimalDenominatorCanonicalization()
	{
		Assert::same('32', (string) new Fraction(8, 1/4));
	}


	public function testAddition()
	{
		$a = new Fraction(1, 4);
		$b = new Fraction(2, 3);

		Assert::same('11/12', (string) $a->add($b));
		Assert::same('11/12', (string) $b->add($a));
	}


	public function testMultiplication()
	{
		$a = new Fraction(1, 4);
		$b = new Fraction(2, 3);

		Assert::same('1/6', (string) $a->multiply($b));
		Assert::same('1/6', (string) $b->multiply($a));
	}


	public function testNegation()
	{
		$a = new Fraction(1/4);

		Assert::same('-1/4', (string) $a->multiply(-1));
	}


	public function testDivision()
	{
		$a = new Fraction(1, 4);
		$b = new Fraction(2, 3);

		Assert::same('3/8', (string) $a->divide($b));
	}


	public function testSgnAndAbs()
	{
		$a = new Fraction(-1/4);

		Assert::same(-1, $a->sgn());
		Assert::same('1/4', (string) $a->absVal());
	}


	public function testComparison()
	{
		$a = new Fraction(2, 3);
		$b = new Fraction(3, 2);

		Assert::true($a->isLowerThan($b));
		Assert::true($b->isGreaterThan($a));
		Assert::false($a->isLowerThan($a));
		Assert::false($a->isGreaterThan($a));
	}

}


id(new FractionTest)->run();
