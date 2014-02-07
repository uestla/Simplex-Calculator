<?php

use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


$z = new Simplex\Func(array(
	'x1' => 2,
	'x2' => 1,
));

$task = new Simplex\Task($z);

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 1,
	'x2' => 1,

), Simplex\Restriction::TYPE_GOE, 2));

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 1,
	'x2' => -1,

), Simplex\Restriction::TYPE_EQ, 0));

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 1,
	'x2' => -2,

), Simplex\Restriction::TYPE_GOE, -4));

$solver = new Simplex\Solver($task);
$steps = $solver->getSteps();

Assert::equal(3 + 4, count($steps));

$exp = array(
	'x1' => 4,
	'x2' => 4,
	'x3' => 6,
	'x4' => 0,
);

foreach (end($steps)->getSolution() as $var => $coeff) {
	Assert::true($coeff->isEqualTo($exp[$var]));
}

Assert::false(current($steps)->hasAlternativeSolution());
Assert::true(current($steps)->getZ()->getB()->isEqualTo(12));
