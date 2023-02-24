<?php

namespace Simplex\Tests;

use Simplex\Func;
use Simplex\Task;
use Tester\Assert;
use Simplex\Solver;
use Tester\TestCase;
use Simplex\Fraction;
use Simplex\Restriction;


require_once __DIR__ . '/bootstrap.php';


final class SolverTest extends TestCase
{

	/** @return void */
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
		Assert::count(4, $solver->getSteps());

		$solution = $solver->getSolution();
		assert(is_array($solution));

		Assert::equal(array(
			'x1' => new Fraction(4),
			'x2' => new Fraction(4),
			'x3' => new Fraction(6),
			'x4' => new Fraction(0),

		), $solution);

		Assert::same('12', (string) $solver->getSolutionValue($solution));

		$altSolutions = $solver->getAlternativeSolutions();
		assert(is_array($altSolutions));
		Assert::count(0, $altSolutions);
	}


	/** @return void */
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
		Assert::count(3, $solver->getSteps());

		$solution = $solver->getSolution();
		assert(is_array($solution));

		Assert::equal(array(
			'x1' => new Fraction(0),
			'x2' => new Fraction(8),
			'x3' => new Fraction(8),
			'x4' => new Fraction(0),

		), $solution);

		Assert::same('16', (string) $solver->getSolutionValue($solution));

		$alternativeSolutions = $solver->getAlternativeSolutions();
		assert(is_array($alternativeSolutions) && count($alternativeSolutions) === 1);

		Assert::equal(array(
			array(
				'x1' => new Fraction(4),
				'x2' => new Fraction(6),
				'x3' => new Fraction(0),
				'x4' => new Fraction(0),
			),

		), $alternativeSolutions);

		Assert::same('16', (string) $solver->getSolutionValue($alternativeSolutions[0]));
	}


	/** @return void */
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
		Assert::count(4, $solver->getSteps());

		$solution = $solver->getSolution();
		assert(is_array($solution));

		Assert::equal(array(
			'x1' => new Fraction(40),
			'x2' => new Fraction(0),
			'x3' => new Fraction(0),
			'x4' => new Fraction(0),
			'x5' => new Fraction(0),
			'x6' => new Fraction(0),

		), $solution);

		Assert::same('6000', (string) $solver->getSolutionValue($solution));

		$altSolutions = $solver->getAlternativeSolutions();
		assert(is_array($altSolutions));
		Assert::count(0, $altSolutions);
	}


	/** @return void */
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
		Assert::count(4, $solver->getSteps());
		Assert::false($solver->getSolution());
	}


	/** @return void */
	public function testTask5()
	{
		// see https://github.com/uestla/Simplex-Calculator/issues/17

		$z = new Func(array(
			'x1' => 371.6,
			'x2' => 348.5,
			'x3' => 36.2,
			'x4' => 899.1,
			'x5' => 46.2,
			'x6' => 386,
			'x7' => 323.6,
			'x8' => 13.27,
			'x9' => 321.6,
			'x10' => 17.3,
			'x11' => 106.8,
			'x12' => 899.1,
		));

		$task = new Task($z);

		// Day kcal constraint:
		$task->addRestriction(new Restriction(array(
			'x1' => 371.6,
			'x2' => 348.5,
			'x3' => 36.2,
			'x4' => 899.1,
			'x5' => 46.2,
			'x6' => 386,
			'x7' => 323.6,
			'x8' => 13.27,
			'x9' => 321.6,
			'x10' => 17.3,
			'x11' => 106.8,
			'x12' => 899.1,

		), Restriction::TYPE_LOE, 1748));

		// Constraints on the kcal of meals:
		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 386,
			'x7' => 323.6,
			'x8' => 13.27,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 350));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 321.6,
			'x10' => 17.3,
			'x11' => 106.8,
			'x12' => 899.1,

		), Restriction::TYPE_LOE, 612));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 46.2,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 175));

		$task->addRestriction(new Restriction(array(
			'x1' => 371.6,
			'x2' => 348.5,
			'x3' => 36.2,
			'x4' => 899.1,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 612));

		// Portion constraints:
		$task->addRestriction(new Restriction(array(
			'x1' => 100,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_GOE, 20));

		$task->addRestriction(new Restriction(array(
			'x1' => 100,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 50));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 100,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_GOE, 30));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 100,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 100));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 100,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_GOE, 150));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 100,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 300));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 100,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_GOE, 5));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 100,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 30));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 100,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_GOE, 150));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 100,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 300));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 100,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_GOE, 30));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 100,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 50));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 100,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_GOE, 5));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 100,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 20));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 100,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_GOE, 40));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 100,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE,40));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 100,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_GOE, 50));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 100,
			'x10' => 0,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 200));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 100,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_GOE, 200));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 100,
			'x11' => 0,
			'x12' => 0,

		), Restriction::TYPE_LOE, 300));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 100,
			'x12' => 0,

		), Restriction::TYPE_GOE, 80));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 100,
			'x12' => 0,

		), Restriction::TYPE_LOE, 300));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 100,

		), Restriction::TYPE_GOE, 5));

		$task->addRestriction(new Restriction(array(
			'x1' => 0,
			'x2' => 0,
			'x3' => 0,
			'x4' => 0,
			'x5' => 0,
			'x6' => 0,
			'x7' => 0,
			'x8' => 0,
			'x9' => 0,
			'x10' => 0,
			'x11' => 0,
			'x12' => 100,

		), Restriction::TYPE_LOE, 30));

		// Constraints on macronutrients:
		$task->addRestriction(new Restriction(array(
			'x1' => 7.800,
			'x2' => 21.800,
			'x3' => 4.600,
			'x4' => 0.000,
			'x5' => 1.200,
			'x6' => 6.600,
			'x7' => 0.600,
			'x8' => 1.150,
			'x9' => 7.460,
			'x10' => 1.300,
			'x11' => 24.000,
			'x12' => 0.000,

		), Restriction::TYPE_LOE, 88));

		$task->addRestriction(new Restriction(array(
			'x1' => 2.80,
			'x2' => 4.90,
			'x3' => 0.20,
			'x4' => 99.90,
			'x5' => 0.60,
			'x6' => 0.80,
			'x7' => 0.00,
			'x8' => 0.83,
			'x9' => 2.08,
			'x10' => 0.10,
			'x11' => 1.20,
			'x12' => 99.90,

		), Restriction::TYPE_LOE, 52));

		$task->addRestriction(new Restriction(array(
			'x1' => 78.80,
			'x2' => 54.30,
			'x3' => 4.00,
			'x4' => 0.00,
			'x5' => 9.00,
			'x6' => 88.10,
			'x7' => 80.30,
			'x8' => 0.30,
			'x9' => 68.26,
			'x10' => 2.80,
			'x11' => 0.00,
			'x12' => 0.00,

		), Restriction::TYPE_LOE, 232));

		// max steps 16
		$solver = new Solver($task);
		Assert::count(16, $solver->getSteps());
		Assert::null($solver->getSolution());
		Assert::null($solver->getAlternativeSolutions());

		// max steps 32
		$solver = new Solver($task, 32);
		Assert::count(23, $solver->getSteps());

		$solution = $solver->getSolution();
		assert(is_array($solution));

		Assert::equal(array(
			'x1' => new Fraction('1','2'),
			'x2' => new Fraction('15751283','20394800'),
			'x3' => new Fraction('3','2'),
			'x4' => new Fraction('1397002663','12224643120'),
			'x5' => new Fraction('3','1'),
			'x6' => new Fraction('1','2'),
			'x7' => new Fraction('1','5'),
			'x8' => new Fraction('2','5'),
			'x9' => new Fraction('22223','32160'),
			'x10' => new Fraction('2','1'),
			'x11' => new Fraction('4','5'),
			'x12' => new Fraction('3','10'),
			'x13' => new Fraction('30593','250'),
			'x14' => new Fraction('21743','250'),
			'x15' => new Fraction('0','1'),
			'x16' => new Fraction('182','5'),
			'x17' => new Fraction('0','1'),
			'x18' => new Fraction('30','1'),
			'x19' => new Fraction('0','1'),
			'x20' => new Fraction('9632843','203948'),
			'x21' => new Fraction('4643517','203948'),
			'x22' => new Fraction('0','1'),
			'x23' => new Fraction('150','1'),
			'x24' => new Fraction('3928852535','611232156'),
			'x25' => new Fraction('11351951365','611232156'),
			'x26' => new Fraction('150','1'),
			'x27' => new Fraction('0','1'),
			'x28' => new Fraction('20','1'),
			'x29' => new Fraction('0','1'),
			'x30' => new Fraction('15','1'),
			'x31' => new Fraction('0','1'),
			'x32' => new Fraction('0','1'),
			'x33' => new Fraction('0','1'),
			'x34' => new Fraction('30715','1608'),
			'x35' => new Fraction('210485','1608'),
			'x36' => new Fraction('0','1'),
			'x37' => new Fraction('100','1'),
			'x38' => new Fraction('0','1'),
			'x39' => new Fraction('220','1'),
			'x40' => new Fraction('25','1'),
			'x41' => new Fraction('0','1'),
			'x42' => new Fraction('31728385577','1223688000'),
			'x43' => new Fraction('0','1'),
			'x44' => new Fraction('5707901407','1223688000'),

		), $solution);

		Assert::same('406407/250', (string) $solver->getSolutionValue($solution));

		$altSolutions = $solver->getAlternativeSolutions();
		assert(is_array($altSolutions));
		Assert::count(0, $altSolutions);
	}

}


id(new SolverTest)->run();
