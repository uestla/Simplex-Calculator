<?php

use Tester\Assert;
use Simplex\Fraction;

require_once __DIR__ . '/bootstrap.php';


Assert::exception(function () {
	new Fraction(3, 0);

}, 'Exception', 'Division by zero.');


$f = new Fraction(0.25);
Assert::equal('1/4', (string) $f);


$f = new Fraction(-0.25);
Assert::equal('-1/4', (string) $f);
Assert::notEqual('1/-4', (string) $f);


$f = new Fraction(-0.25, -1);
Assert::equal('1/4', (string) $f);


$f = new Fraction(8, 1/4);
Assert::equal('32', (string) $f);


$a = new Fraction(1, 4);
$b = new Fraction(2, 3);
Assert::equal('11/12', (string) $a->add($b));
Assert::equal('11/12', (string) $b->add($a));


$a = new Fraction(1, 4);
$b = new Fraction(2, 3);
Assert::equal('1/6', (string) $a->multiply($b));
Assert::equal('1/6', (string) $b->multiply($a));


$a = new Fraction(1, 4);
$b = new Fraction(2, 3);
Assert::equal('3/8', (string) $a->divide($b));


$a = new Fraction(1/4);
Assert::equal('-1/4', (string) $a->multiply(-1));


$a = new Fraction(-1/4);
Assert::equal(-1, $a->sgn());
Assert::equal('1/4', (string) $a->absVal());


$a = new Fraction(2, 3);
$b = new Fraction(3, 2);
Assert::true($a->isLowerThan($b));
Assert::true($b->isGreaterThan($a));
Assert::false($a->isLowerThan($a));
Assert::false($a->isGreaterThan($a));
