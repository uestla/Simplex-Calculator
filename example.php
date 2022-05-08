<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$z = new Simplex\Func([
    'x1' => 1,
    'x2' => 2,
]);

$task = new Simplex\Task($z);

$task->addRestriction(new Simplex\Restriction([
    'x1' => 3,
    'x2' => 2,

], Simplex\Restriction::TYPE_LOE, 24));

$task->addRestriction(new Simplex\Restriction([
    'x1' => -2,
    'x2' => -4,

], Simplex\Restriction::TYPE_GOE, -32));

$solver = new Simplex\Solver($task);

var_dump($solver);
die;
