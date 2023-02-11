<?php

namespace Simplex\Tests;

use Tester\Assert;
use Simplex\Helpers;
use Tester\TestCase;


require_once __DIR__ . '/bootstrap.php';


final class HelpersTest extends TestCase
{

	public function testMain()
	{
		Assert::same(1, Helpers::sgn(7));
		Assert::same(-1, Helpers::sgn(-7));
		Assert::same(0, Helpers::sgn(0));

		Assert::same('3', Helpers::gcd(6, 27));
		Assert::same('3', Helpers::gcd(-6, 27));
		Assert::same('3', Helpers::gcd(6, -27));
		Assert::same('3', Helpers::gcd(-6, -27));

		Assert::same('1', Helpers::gcd(1, 24));
		Assert::same('21', Helpers::gcd(0, 21));
		Assert::same('256', Helpers::gcd(1400000000000000256, 100000000000000000));

		Assert::exception(function () {
			Helpers::gcd(0, 0);

		}, 'InvalidArgumentException', 'At least one number must not be a zero.');

		Assert::exception(function () {
			Helpers::gcd('asDF', 1);

		}, 'InvalidArgumentException', 'Integers expected for gcd.');
	}

}


id(new HelpersTest)->run();
