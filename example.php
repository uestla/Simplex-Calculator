<?php

require_once __DIR__ . '/Simplex/Simplex.php';


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

var_dump($solver); die();
