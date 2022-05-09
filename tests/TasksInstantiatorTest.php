<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Simplex\Formulation\Equal;
use Simplex\Formulation\Formula;
use Simplex\Formulation\GreaterOrEqual;
use Simplex\Formulation\LinearProgram;

class TasksInstantiatorTest extends TestCase
{
    use TaskCreator;

    public function test_should_all_constraints_be_constraints(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("All \$constraints have to be instance of \Simplex\Constraint");

        new LinearProgram(
            new Formula(2, 1),
            [
                new GreaterOrEqual(new Formula(1, 1), 2),
                new Equal(new Formula(1, -1), 0),
                null
            ]
        );
    }

    public function test_should_all_formula_has_same_length(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage("Number of parameters are not the same for each formula");

        new LinearProgram(
            new Formula(2, 1),
            [
                new GreaterOrEqual(new Formula(1), 2),
                new Equal(new Formula(1, -1, 2), 0),
            ]
        );
    }

    public function test_task_instantiate(): void
    {
        /**
         * We have given linear problem
         *  z = 2x1 + x2
         * with constraints:
         *  x1 + x2 >= 2
         *  x1 - x2 = 0
         *  x1 - x2 >= -4
         *
         * that we can describe using \Simplex\Formulation\LinearProgram
         */
        $program = new LinearProgram(
            new Formula(2, 1),
            [
                new GreaterOrEqual(new Formula(1, 1), 2),
                new Equal(new Formula(1, -1), 0),
                new GreaterOrEqual(new Formula(1, -2), -4),
            ]
        );

        $params = array('x1' => 4, 'x2' => 4, 'x3' => 6, 'x4' => 0);
        $formula = new Formula($params);

        $this->assertCount(3 + 4, $program->getSolution()->getSteps());
        $this->assertEquals($params, $program->getOptimizedParams());
        $this->assertEquals($formula, $program->getSolutionFormula());
        $this->assertEquals($params['x1'], $program->getSolutionFormula()->getParam('x1'));
        $this->assertEquals($params['x1'], $program->getSolutionFormula()->getParam(1));
        $this->assertEquals(12, $program->getMax());
    }
}