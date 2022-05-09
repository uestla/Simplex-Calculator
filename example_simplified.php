<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Simplex\Formulation\Equal;
use Simplex\Formulation\Formula;
use Simplex\Formulation\GreaterOrEqual;
use Simplex\Formulation\LinearProgram;
use Simplex\Formulation\LowerOrEqual;

$program = new LinearProgram(
    new Formula(1, 2),
    [
        new LowerOrEqual(new Formula(3, 2), 24),
        new GreaterOrEqual(new Formula(-2, -4), -32),
    ]
);

var_dump(
    $program->getMax(), // 16
    $program->getOptimizedParams(), // array(4) { ["x1"]=>float(4) ["x2"]=>float(6) ["x3"]=>float(0) ["x4"]=>float(0) }
    $program->getSolutionFormula(), // upper value but in Simplex\Formulation\Formula
);