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
