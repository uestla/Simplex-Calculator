<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Simplex\Func;
use Simplex\Restriction;
use Simplex\Solution;
use Simplex\Solver;
use Simplex\Task;

class TasksTest extends TestCase
{
    use TaskCreator;

    public function test_task_restriction_variables(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Restriction variables don't match the objective function variables.");

        $task = new Task(new Func(array(
            'x1' => 4,
        )));

        $task->addRestriction(new Restriction(array(
            'x2' => 5,
        ), Restriction::TYPE_EQ, 4));
    }

    public function test_first_task_function(): void
    {
        $task = $this->getFirstTask();

        $solver = new Solver($task);
        $steps = $solver->getSteps();

        $this->assertCount(3 + 4, $steps);

        $exp = array(
            'x1' => 4,
            'x2' => 4,
            'x3' => 6,
            'x4' => 0,
        );

        foreach (end($steps)->getSolution() as $var => $coeff) {
            $this->assertTrue($coeff->isEqualTo($exp[$var]));
        }

        $this->assertFalse(current($steps)->hasAlternativeSolution());
        $this->assertEquals(12, current($steps)->getZ()->getB()->toFloat());
    }

    public function test_first_task_with_solution_instance(): void
    {
        $task = $this->getFirstTask();

        $solver = new Solver($task);
        $solution = Solution::instantiateFromSolver($solver);

        $exp = array(
            'x1' => 4,
            'x2' => 4,
            'x3' => 6,
            'x4' => 0,
        );

        $this->assertCount(3 + 4, $solution->getSteps());
        $this->assertEquals($exp, $solution->getSolutionParams());
        $this->assertEquals(12, $solution->getMax());
    }

    public function test_first_task_as_builder_solution(): void
    {
        $task = $this->getFirstTask();

        $solution = Solution::build($task);

        $exp = array(
            'x1' => 4,
            'x2' => 4,
            'x3' => 6,
            'x4' => 0,
        );

        $this->assertCount(3 + 4, $solution->getSteps());
        $this->assertEquals($exp, $solution->getSolutionParams());
        $this->assertEquals(12, $solution->getMax());
    }

    public function test_second_task_function(): void
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

        $this->assertCount(3 + 3, $steps);

        $exp1 = array(
            'x1' => 4,
            'x2' => 6,
            'x3' => 0,
            'x4' => 0,
        );

        foreach (end($steps)->getSolution() as $var => $coeff) {
            $this->assertTrue($coeff->isEqualTo($exp1[$var]));
        }

        $this->assertTrue(current($steps)->getZ()->getB()->isEqualTo(16));

        $exp2 = array(
            'x1' => 0,
            'x2' => 8,
            'x3' => 8,
            'x4' => 0,
        );

        foreach (prev($steps)->getSolution() as $var => $coeff) {
            $this->assertTrue($coeff->isEqualTo($exp2[$var]));
        }

        $this->assertTrue(current($steps)->hasAlternativeSolution());
        $this->assertTrue(current($steps)->getZ()->getB()->isEqualTo(16));
    }

    public function test_third_function(): void
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

        $solver = new \Simplex\Solver($task);
        $steps = $solver->getSteps();

        $this->assertCount(3 + 4, $steps);

        $exp = array(
            'x1' => 40,
            'x2' => 0,
            'x3' => 0,
            'x4' => 0,
            'x5' => 0,
            'x6' => 0,
        );

        foreach (end($steps)->getSolution() as $var => $coeff) {
            $this->assertTrue($coeff->isEqualTo($exp[$var]));
        }

        $this->assertFalse(current($steps)->hasAlternativeSolution());
        $this->assertTrue(current($steps)->getZ()->getB()->isEqualTo(6000));
    }

    public function test_fourth_function(): void
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

        $solver = new \Simplex\Solver($task);
        $steps = $solver->getSteps();

        $this->assertCount(3 + 4, $steps);
        $this->assertFalse(end($steps)->hasSolution());
    }
}