<?php

use Simplex\Printer;


require_once __DIR__ . '/Simplex/simplex.php';


// define objective functions
$z = new Simplex\Func(array(
	'x1' => 1,
	'x2' => 2,
));

$task = new Simplex\Task($z);

// add constraints
$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 3,
	'x2' => 2,

), Simplex\Restriction::TYPE_LOE, 24));

$task->addRestriction(new Simplex\Restriction(array(
	'x1' => -2,
	'x2' => -4,

), Simplex\Restriction::TYPE_GOE, -32));

// get solutions
$solver = new Simplex\Solver($task);

$solution = $solver->getSolution();
$alternativeSolutions = $solver->getAlternativeSolution();

// print solver
$printer = new Printer;
$printer->printSolver($solver);
