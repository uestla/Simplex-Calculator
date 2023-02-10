<?php

namespace Simplex\Tests;

use Tester\Assert;

use Simplex\Task;
use Simplex\Func;
use Tester\TestCase;
use Simplex\Restriction;

require_once __DIR__ . '/bootstrap.php';


final class TaskTest extends TestCase
{

	public function testRestrictionAndObjectiveVariablesMismatch()
	{
		Assert::exception(function () {
			$task = new Task(new Func(array(
				'x1' => 4,
			)));

			$task->addRestriction(new Restriction(array(
				'x2' => 5,
			), Restriction::TYPE_EQ, 4));

		}, 'InvalidArgumentException', "Restriction variables don't match the objective function variables.");
	}

}


id(new TaskTest)->run();
