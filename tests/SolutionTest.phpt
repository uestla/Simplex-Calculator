<?php

use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';


// === TASK 1 =========================================

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



// === TASK 3 =========================================

$z = new Simplex\Func(array(
	'x1' => 150,
	'x2' => 200,
	'x3' => 200,
));

$task = new Simplex\Task($z);

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 2,
	'x2' => 3,
	'x3' => 1,

), Simplex\Restriction::TYPE_LOE, 80));

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 3,
	'x2' => 6,
	'x3' => 4,

), Simplex\Restriction::TYPE_LOE, 120));

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 0,
	'x2' => -1,
	'x3' => 4,

), Simplex\Restriction::TYPE_LOE, 0));

$solver = new Simplex\Solver($task);
$steps = $solver->getSteps();

Assert::equal(3 + 4, count($steps));

$exp = array(
	'x1' => 40,
	'x2' => 0,
	'x3' => 0,
	'x4' => 0,
	'x5' => 0,
	'x6' => 0,
);

foreach (end($steps)->getSolution() as $var => $coeff) {
	Assert::true($coeff->isEqualTo($exp[$var]));
}

Assert::false(current($steps)->hasAlternativeSolution());
Assert::true(current($steps)->getZ()->getB()->isEqualTo(6000));



// === TASK 4 =========================================

$z = new Simplex\Func(array(
	'x1' => -2,
	'x2' => 1,
));

$task = new Simplex\Task($z);

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => -1,
	'x2' => 1,

), Simplex\Restriction::TYPE_LOE, -1));

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 1,
	'x2' => 0,

), Simplex\Restriction::TYPE_LOE, 4));

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 1,
	'x2' => 2,

), Simplex\Restriction::TYPE_GOE, 14));

$solver = new Simplex\Solver($task);
$steps = $solver->getSteps();

Assert::equal(3 + 4, count($steps));
Assert::false(end($steps)->hasSolution());
