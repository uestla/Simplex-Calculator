<?php

namespace Simplex\Formulation;

use Simplex\Func;
use Simplex\Restriction;
use Simplex\Solution;
use Simplex\Solver;
use Simplex\Task;
use Simplex\VariableSet;

class LinearProgram
{
    protected Formula $goal;
    protected array $constraints;
    protected Task $task;
    protected Solution $solution;

    /**
     * LinearProgram constructor.
     */
    public function __construct(Formula $goal, array $constraints)
    {
        $this->validate($goal, $constraints);

        $this->goal = $goal;
        $this->constraints = $constraints;

        $this->makeTask();
        $this->solution = Solution::build($this->task);
    }

    private function validate(Formula $goal, array $constraints): void
    {
        foreach ($constraints as $constraint) {
            if (!$constraint instanceof Constraint) {
                throw new \InvalidArgumentException("All \$constraints have to be instance of \Simplex\Constraint");
            }

            if ($constraint->getFormula()->getSize() !== $goal->getSize()) {
                throw new \OutOfBoundsException("Number of parameters are not the same for each formula");
            }
        }
    }

    private function makeTask(): void
    {
        $z = new Func($this->goal->getCoefficients());
        $this->task = new Task($z);

        /** @var Constraint $constraint */
        foreach ($this->constraints as $constraint) {
            $restriction = [
                GreaterOrEqual::class => Restriction::TYPE_GOE,
                LowerOrEqual::class => Restriction::TYPE_LOE,
                Equal::class => Restriction::TYPE_EQ,
            ][get_class($constraint)];

            $this->task->addRestriction(
                new Restriction(
                    $constraint->getFormula()->getCoefficients(),
                    $restriction,
                    $constraint->getConstraint()
                )
            );
        }
    }

    public function getSolution(): Solution
    {
        return $this->solution;
    }

    public function getOptimizedParams(): array
    {
        return $this->solution->getSolutionParams();
    }

    public function getMax(): float
    {
        return $this->solution->getMax();
    }

    public function getSolutionFormula(): Formula
    {
        return new Formula($this->solution->getSolutionParams());
    }

}