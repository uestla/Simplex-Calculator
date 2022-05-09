<?php

declare(strict_types=1);

namespace Simplex;

class Solution
{
    private ?array $solution;
    private array $steps;
    private TableRow $z;

    /**
     * Solution constructor.
     */
    public function __construct(array $steps)
    {
        $this->steps = $steps;
        $this->solution = end($steps)->getSolution() ?: null;
        $this->z = current($steps)->getZ();
    }

    public static function instantiateFromSolver(Solver $solver): self
    {
        return new self($solver->getSteps());
    }

    public static function build(Task $task): self
    {
        return static::instantiateFromSolver(new Solver($task));
    }

    public function getSolution(): ?array
    {
        return $this->solution;
    }

    public function getSolutionParams(): array
    {
        $solutionParameters = [];
        foreach ($this->getSolution() as $variable => $value) {
            $solutionParameters[$variable] = $value->toFloat();
        }

        return $solutionParameters;
    }

    public function getMaxFraction(): Fraction
    {
        return $this->z->getB();
    }

    public function getMax(): float
    {
        return $this->getMaxFraction()->toFloat();
    }

    public function getSteps(): array
    {
        return $this->steps;
    }
}