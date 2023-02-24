<?php

namespace Simplex\Tests;

use Simplex\Func;
use Simplex\Task;
use Tester\Assert;
use Simplex\Solver;
use Simplex\Printer;
use Tester\TestCase;
use Simplex\Restriction;


require_once __DIR__ . '/bootstrap.php';


final class PrinterTest extends TestCase
{

	/** @return void */
	public function testPrintSolver()
	{
		$task = new Task(new Func(array(
			'x1' => 2,
			'x2' => 1,
		)));

		$task->addRestriction(new Restriction(array(
			'x1' => 1,
			'x2' => 1,

		), Restriction::TYPE_GOE, 2));

		$task->addRestriction(new Restriction(array(
			'x1' => 1,
			'x2' => -1,

		), Restriction::TYPE_EQ, 0));

		$task->addRestriction(new Restriction(array(
			'x1' => 1,
			'x2' => -2,

		), Restriction::TYPE_GOE, -4));

		$solver = new Solver($task);

		$printer = new Printer;

		ob_start(function () {});
		$printer->printSolver($solver);
		$output = ob_get_flush();

		Assert::same('TASK ----------------------

Maximize:
  2x1 + x2

subject to:
  x1 + x2 ≥ 2
  x1 - x2 = 0
  x1 - 2x2 ≥ -4

SOLUTION STEPS ------------

1. Standard task transformation
Maximize:
  2x1 + x2 + 0x3 + 0y1 + 0y2 + 0x4

subject to:
  x1 + x2 - x3 + y1 + 0y2 + 0x4 = 2
  x1 - x2 + 0x3 + 0y1 + y2 + 0x4 = 0
  -x1 + 2x2 + 0x3 + 0y1 + 0y2 + x4 = 4

2. Simplex tableaus

Tableau #1
    | x1 | x2 | x3 | y1 | y2 | x4 ||  b | t |
----+----+----+----+----+----+----++----+---+
 y1 |  1 |  1 | -1 |  1 |  0 |  0 ||  2 | 2 |
 y2 |  1 | -1 |  0 |  0 |  1 |  0 ||  0 | 0 |
 x4 | -1 |  2 |  0 |  0 |  0 |  1 ||  4 | - |
----+----+----+----+----+----+----++----+---+
  z | -2 | -1 |  0 |  0 |  0 |  0 ||  0 |   |
 z2 | -2 |  0 |  1 |  0 |  0 |  0 || -2 |   |


Tableau #2
    | x1 | x2 | x3 | y1 | y2 | x4 ||  b | t |
----+----+----+----+----+----+----++----+---+
 y1 |  0 |  2 | -1 |  1 | -1 |  0 ||  2 | 1 |
 x1 |  1 | -1 |  0 |  0 |  1 |  0 ||  0 | - |
 x4 |  0 |  1 |  0 |  0 |  1 |  1 ||  4 | 4 |
----+----+----+----+----+----+----++----+---+
  z |  0 | -3 |  0 |  0 |  2 |  0 ||  0 |   |
 z2 |  0 | -2 |  1 |  0 |  2 |  0 || -2 |   |


Tableau #3
    | x1 | x2 |   x3 |   y1 |   y2 | x4 || b | t |
----+----+----+------+------+------+----++---+---+
 x2 |  0 |  1 | -1/2 |  1/2 | -1/2 |  0 || 1 | - |
 x1 |  1 |  0 | -1/2 |  1/2 |  1/2 |  0 || 1 | - |
 x4 |  0 |  0 |  1/2 | -1/2 |  3/2 |  1 || 3 | 6 |
----+----+----+------+------+------+----++---+---+
  z |  0 |  0 | -3/2 |  3/2 |  1/2 |  0 || 3 |   |
 z2 |  0 |  0 |    0 |    1 |    1 |  0 || 0 |   |


Tableau #4
    | x1 | x2 | x3 | x4 ||  b |
----+----+----+----+----++----+
 x2 |  0 |  1 |  0 |  1 ||  4 |
 x1 |  1 |  0 |  0 |  1 ||  4 |
 x3 |  0 |  0 |  1 |  2 ||  6 |
----+----+----+----+----++----+
  z |  0 |  0 |  0 |  3 || 12 |


OPTIMAL SOLUTION ----------
<4, 4> = 12
', $output);
	}


	/** @return void */
	public function testPrintSolverAlternativeSolution()
	{
		$task = new Task(new Func(array(
			'x1' => 1,
			'x2' => 2,
		)));

		$task->addRestriction(new Restriction(array(
			'x1' => 3,
			'x2' => 2,

		), Restriction::TYPE_LOE, 24));

		$task->addRestriction(new Restriction(array(
			'x1' => -2,
			'x2' => -4,

		), Restriction::TYPE_GOE, -32));

		$solver = new Solver($task);

		$printer = new Printer;

		ob_start(function () {});
		$printer->printSolver($solver);
		$output = ob_get_flush();

		Assert::same('TASK ----------------------

Maximize:
  x1 + 2x2

subject to:
  3x1 + 2x2 ≤ 24
  -2x1 - 4x2 ≥ -32

SOLUTION STEPS ------------

1. Standard task transformation
Maximize:
  x1 + 2x2 + 0x3 + 0x4

subject to:
  3x1 + 2x2 + x3 + 0x4 = 24
  2x1 + 4x2 + 0x3 + x4 = 32

2. Simplex tableaus

Tableau #1
    | x1 | x2 | x3 | x4 ||  b |  t |
----+----+----+----+----++----+----+
 x3 |  3 |  2 |  1 |  0 || 24 | 12 |
 x4 |  2 |  4 |  0 |  1 || 32 |  8 |
----+----+----+----+----++----+----+
  z | -1 | -2 |  0 |  0 ||  0 |    |


Tableau #2
    |  x1 | x2 | x3 |   x4 ||  b |
----+-----+----+----+------++----+
 x3 |   2 |  0 |  1 | -1/2 ||  8 |
 x2 | 1/2 |  1 |  0 |  1/4 ||  8 |
----+-----+----+----+------++----+
  z |   0 |  0 |  0 |  1/2 || 16 |


Tableau #3
    | x1 | x2 |   x3 |   x4 ||  b |
----+----+----+------+------++----+
 x1 |  1 |  0 |  1/2 | -1/4 ||  4 |
 x2 |  0 |  1 | -1/4 |  3/8 ||  6 |
----+----+----+------+------++----+
  z |  0 |  0 |    0 |  1/2 || 16 |


OPTIMAL SOLUTION ----------
λ<0, 8> + (1 - λ)<4, 6> = 16
', $output);
	}


	/** @return void */
	public function testPrintSolverMaxStepsReached()
	{
		$task = new Task(new Func(array(
			'x1' => 2,
			'x2' => 1,
		)));

		$task->addRestriction(new Restriction(array(
			'x1' => 1,
			'x2' => 1,

		), Restriction::TYPE_GOE, 2));

		$task->addRestriction(new Restriction(array(
			'x1' => 1,
			'x2' => -1,

		), Restriction::TYPE_EQ, 0));

		$task->addRestriction(new Restriction(array(
			'x1' => 1,
			'x2' => -2,

		), Restriction::TYPE_GOE, -4));

		$solver = new Solver($task, 1);

		$printer = new Printer;

		ob_start(function () {});
		$printer->printSolver($solver);
		$output = ob_get_flush();

		Assert::same('TASK ----------------------

Maximize:
  2x1 + x2

subject to:
  x1 + x2 ≥ 2
  x1 - x2 = 0
  x1 - 2x2 ≥ -4

SOLUTION STEPS ------------

1. Standard task transformation
Maximize:
  2x1 + x2 + 0x3 + 0y1 + 0y2 + 0x4

subject to:
  x1 + x2 - x3 + y1 + 0y2 + 0x4 = 2
  x1 - x2 + 0x3 + 0y1 + y2 + 0x4 = 0
  -x1 + 2x2 + 0x3 + 0y1 + 0y2 + x4 = 4

2. Simplex tableaus

Tableau #1
    | x1 | x2 | x3 | y1 | y2 | x4 ||  b | t |
----+----+----+----+----+----+----++----+---+
 y1 |  1 |  1 | -1 |  1 |  0 |  0 ||  2 | 2 |
 y2 |  1 | -1 |  0 |  0 |  1 |  0 ||  0 | 0 |
 x4 | -1 |  2 |  0 |  0 |  0 |  1 ||  4 | - |
----+----+----+----+----+----+----++----+---+
  z | -2 | -1 |  0 |  0 |  0 |  0 ||  0 |   |
 z2 | -2 |  0 |  1 |  0 |  0 |  0 || -2 |   |


Tableau #2
    | x1 | x2 | x3 | y1 | y2 | x4 ||  b | t |
----+----+----+----+----+----+----++----+---+
 y1 |  0 |  2 | -1 |  1 | -1 |  0 ||  2 | 1 |
 x1 |  1 | -1 |  0 |  0 |  1 |  0 ||  0 | - |
 x4 |  0 |  1 |  0 |  0 |  1 |  1 ||  4 | 4 |
----+----+----+----+----+----+----++----+---+
  z |  0 | -3 |  0 |  0 |  2 |  0 ||  0 |   |
 z2 |  0 | -2 |  1 |  0 |  2 |  0 || -2 |   |


OPTIMAL SOLUTION ----------
Maximum number of 1 steps reached.
', $output);
	}


	/** @return void */
	public function testPrintSolverNoSolution()
	{
		$task = new Task(new Func(array(
			'x1' => -2,
			'x2' => 1,
		)));

		$task->addRestriction(new Restriction(array(
			'x1' => -1,
			'x2' => 1,

		), Restriction::TYPE_LOE, -1));

		$task->addRestriction(new Restriction(array(
			'x1' => 1,
			'x2' => 0,

		), Restriction::TYPE_LOE, 4));

		$task->addRestriction(new Restriction(array(
			'x1' => 1,
			'x2' => 2,

		), Restriction::TYPE_GOE, 14));

		$solver = new Solver($task);

		$printer = new Printer;

		ob_start(function () {});
		$printer->printSolver($solver);
		$output = ob_get_flush();

		Assert::same('TASK ----------------------

Maximize:
  -2x1 + x2

subject to:
  -x1 + x2 ≤ -1
  x1 + 0x2 ≤ 4
  x1 + 2x2 ≥ 14

SOLUTION STEPS ------------

1. Standard task transformation
Maximize:
  -2x1 + x2 + 0x3 + 0y1 + 0x4 + 0x5 + 0y2

subject to:
  x1 - x2 - x3 + y1 + 0x4 + 0x5 + 0y2 = 1
  x1 + 0x2 + 0x3 + 0y1 + x4 + 0x5 + 0y2 = 4
  x1 + 2x2 + 0x3 + 0y1 + 0x4 - x5 + y2 = 14

2. Simplex tableaus

Tableau #1
    | x1 | x2 | x3 | y1 | x4 | x5 | y2 ||   b |  t |
----+----+----+----+----+----+----+----++-----+----+
 y1 |  1 | -1 | -1 |  1 |  0 |  0 |  0 ||   1 |  1 |
 x4 |  1 |  0 |  0 |  0 |  1 |  0 |  0 ||   4 |  4 |
 y2 |  1 |  2 |  0 |  0 |  0 | -1 |  1 ||  14 | 14 |
----+----+----+----+----+----+----+----++-----+----+
  z |  2 | -1 |  0 |  0 |  0 |  0 |  0 ||   0 |    |
 z2 | -2 | -1 |  1 |  0 |  0 |  1 |  0 || -15 |    |


Tableau #2
    | x1 | x2 | x3 | y1 | x4 | x5 | y2 ||   b |    t |
----+----+----+----+----+----+----+----++-----+------+
 x1 |  1 | -1 | -1 |  1 |  0 |  0 |  0 ||   1 |    - |
 x4 |  0 |  1 |  1 | -1 |  1 |  0 |  0 ||   3 |    3 |
 y2 |  0 |  3 |  1 | -1 |  0 | -1 |  1 ||  13 | 13/3 |
----+----+----+----+----+----+----+----++-----+------+
  z |  0 |  1 |  2 | -2 |  0 |  0 |  0 ||  -2 |      |
 z2 |  0 | -3 | -1 |  2 |  0 |  1 |  0 || -13 |      |


Tableau #3
    | x1 | x2 | x3 | y1 | x4 | x5 | y2 ||  b | t |
----+----+----+----+----+----+----+----++----+---+
 x1 |  1 |  0 |  0 |  0 |  1 |  0 |  0 ||  4 | - |
 x2 |  0 |  1 |  1 | -1 |  1 |  0 |  0 ||  3 | - |
 y2 |  0 |  0 | -2 |  2 | -3 | -1 |  1 ||  4 | 2 |
----+----+----+----+----+----+----+----++----+---+
  z |  0 |  0 |  1 | -1 | -1 |  0 |  0 || -5 |   |
 z2 |  0 |  0 |  2 | -1 |  3 |  1 |  0 || -4 |   |


Tableau #4
    | x1 | x2 | x3 | y1 |   x4 |   x5 |  y2 ||  b | t |
----+----+----+----+----+------+------+-----++----+---+
 x1 |  1 |  0 |  0 |  0 |    1 |    0 |   0 ||  4 | 4 |
 x2 |  0 |  1 |  0 |  0 | -1/2 | -1/2 | 1/2 ||  5 | - |
 y1 |  0 |  0 | -1 |  1 | -3/2 | -1/2 | 1/2 ||  2 | - |
----+----+----+----+----+------+------+-----++----+---+
  z |  0 |  0 |  0 |  0 | -5/2 | -1/2 | 1/2 || -3 |   |
 z2 |  0 |  0 |  1 |  0 |  3/2 |  1/2 | 1/2 || -2 |   |


OPTIMAL SOLUTION ----------
Solution not found.
', $output);
	}

}


id (new PrinterTest)->run();
