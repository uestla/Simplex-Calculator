<?php

namespace Simplex\Tests;

use Tester\Assert;

use Simplex\Table;
use Tester\TestCase;
use Simplex\TableRow;
use Simplex\ValueFunc;

require_once __DIR__ . '/bootstrap.php';


final class TableTest extends TestCase
{

	/** @return void */
	public function testBasisGetter()
	{
		$t = new Table(new ValueFunc(array(
			'x1' => -1,
			'x2' => -2,
			'x3' => 0,
			'x4' => 0,

		), 0));

		$t->addRow(new TableRow('x3', array(
			'x1' => 3,
			'x2' => 2,
			'x3' => 1,
			'x4' => 0,

		), 24));

		$t->addRow(new TableRow('x4', array(
			'x1' => 2,
			'x2' => 4,
			'x3' => 0,
			'x4' => 1,

		), 32));

		Assert::same(array('x3', 'x4'), $t->getBasis());

		$t->nextStep();
		Assert::same(array('x3', 'x2'), $t->getBasis());

		$t->nextStep();
		Assert::same(array('x1', 'x2'), $t->getBasis());
	}


	/** @return void */
	public function testObjectiveVariablesMismatch()
	{
		Assert::exception(function () {
			$z = new ValueFunc(array(
				'x1' => 5,
			), 13);

			$z2 = new ValueFunc(array(
				'x2' => 7,
			), 43);

			new Table($z, $z2);

		}, 'InvalidArgumentException', "Variables of both objective functions don't match.");
	}


	/** @return void */
	public function testRowAndObjectiveVariablesMismatch()
	{
		Assert::exception(function () {
			$t = new Table(new ValueFunc(array(
				'x1' => 4,
			), 42));

			$t->addRow(new TableRow('x1', array(
				'x2' => 5,
			), 14));

		}, 'InvalidArgumentException', "Row variables don't match the objective function variables.");
	}

}


id(new TableTest)->run();
