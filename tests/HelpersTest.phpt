<?php

use Tester\Assert;
use Simplex\Helpers;

require_once __DIR__ . '/bootstrap.php';

Assert::true(Helpers::isInt('0785'));
Assert::true(Helpers::isInt('-788'));
Assert::true(Helpers::isInt('-788e8'));
Assert::false(Helpers::isInt(NULL));
Assert::false(Helpers::isInt(TRUE));
Assert::false(Helpers::isInt(array()));
Assert::false(Helpers::isInt('a'));

Assert::equal(1, Helpers::sgn(7));
Assert::equal(-1, Helpers::sgn(-7));
Assert::equal(0, Helpers::sgn(0));

Assert::equal(3, Helpers::gcd(6, 27));
Assert::equal(3, Helpers::gcd(-6, 27));
Assert::equal(3, Helpers::gcd(6, -27));
Assert::equal(3, Helpers::gcd(-6, -27));

Assert::equal(1, Helpers::gcd(1, 24));
Assert::equal(21, Helpers::gcd(0, 21));

Assert::exception(function () {
	Helpers::gcd(0, 0);

}, 'InvalidArgumentException', 'At least one number must not be a zero.');

Assert::exception(function () {
	Helpers::gcd('ahoj', 1);

}, 'InvalidArgumentException', 'Integers expected for gcd.');
