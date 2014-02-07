<?php

use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


// === TASK 2 =========================================

$z = new Simplex\Func(array(
	'x1' => 1,
	'x2' => 2,
));

$task = new Simplex\Task($z);

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 3,
	'x2' => 2,

), Simplex\Restriction::TYPE_LOE, 24));

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => -2,
	'x2' => -4,

), Simplex\Restriction::TYPE_GOE, -32));


$solver = new Simplex\Solver($task);
$steps = $solver->getSteps();

Assert::equal(3 + 3, count($steps));

$exp1 = array(
	'x1' => 4,
	'x2' => 6,
	'x3' => 0,
	'x4' => 0,
);

foreach (end($steps)->getSolution() as $var => $coeff) {
	Assert::true($coeff->isEqualTo($exp1[$var]));
}

Assert::true(current($steps)->getZ()->getB()->isEqualTo(16));

$exp2 = array(
	'x1' => 0,
	'x2' => 8,
	'x3' => 8,
	'x4' => 0,
);

foreach (prev($steps)->getSolution() as $var => $coeff) {
	Assert::true($coeff->isEqualTo($exp2[$var]));
}

Assert::true(current($steps)->hasAlternativeSolution());
Assert::true(current($steps)->getZ()->getB()->isEqualTo(16));
