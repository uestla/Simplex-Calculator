<?php

namespace Simplex\Tests;

use Simplex\Func;
use Simplex\Task;
use Tester\Assert;
use Simplex\Solver;
use Tester\TestCase;
use Simplex\Restriction;


require_once __DIR__ . '/bootstrap.php';


final class SolverTest extends TestCase
{

	public function testTask1()
	{
		$z = new Func(array(
			'x1' => 2,
			'x2' => 1,
		));

		$task = new Task($z);

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
		$steps = $solver->getSteps();

		Assert::count(3 + 4, $steps);

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
	}


	public function testTask2()
	{
		$z = new Func(array(
			'x1' => 1,
			'x2' => 2,
		));

		$task = new Task($z);

		$task->addRestriction(new Restriction(array(
			'x1' => 3,
			'x2' => 2,

		), Restriction::TYPE_LOE, 24));

		$task->addRestriction(new Restriction(array(
			'x1' => -2,
			'x2' => -4,

		), Restriction::TYPE_GOE, -32));


		$solver = new Solver($task);
		$steps = $solver->getSteps();

		Assert::count(3 + 3, $steps);

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
	}


	public function testTask3()
	{
		$z = new Func(array(
			'x1' => 150,
			'x2' => 200,
			'x3' => 200,
		));

		$task = new Task($z);

		$task->addRestriction(new Restriction(array(
			'x1' => 2,
			'x2' => 3,
			'x3' => 1,

		), Restriction::TYPE_LOE, 80));

		$task->addRestriction(new Restriction(array(
			'x1' => 3,
			'x2' => 6,
			'x3' => 4,

		), Restriction::TYPE_LOE, 120));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => -1,
			'x3' => 4,

		), Restriction::TYPE_LOE, 0));

		$solver = new Solver($task);
		$steps = $solver->getSteps();

		Assert::count(3 + 4, $steps);

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
	}


	public function testTask4()
	{
		$z = new Func(array(
			'x1' => -2,
			'x2' => 1,
		));

		$task = new Task($z);

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
		$steps = $solver->getSteps();

		Assert::count(3 + 4, $steps);
		Assert::false(end($steps)->hasSolution());
	}

}


id(new SolverTest)->run();
