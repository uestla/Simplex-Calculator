<?php

declare(strict_types=1);

/**
 * This file is part of the SimplexCalculator library
 *
 * Copyright (c) 2014 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 *
 * @link     https://github.com/uestla/Simplex-Calculator
 */

namespace Simplex;

class Solver
{
    /** @var array */
    private array $steps = [];

    private int $maxSteps;

    public function __construct(Task $task, int $maxSteps = 16)
    {
        $this->maxSteps = (int) $maxSteps;
        $this->steps[] = $task;

        $this->solve();
    }

    /** @return array */
    public function getSteps(): array
    {
        return $this->steps;
    }

    private function solve(): void
    {
        $t = clone reset($this->steps);
        $this->steps[] = $t->fixRightSides();

        $t = clone $t;
        $this->steps[] = $t->fixNonEquations();

        $this->steps[] = $tbl = $t->toTable();
        while (! $tbl->isSolved()) {
            $tbl = clone $tbl;
            $this->steps[] = $tbl->nextStep();

            if (count($this->steps) > $this->maxSteps) {
                break;
            }
        }

        if ($tbl->hasAlternativeSolution()) {
            $this->steps[] = $tbl->getAlternativeSolution();
        }
    }
}
