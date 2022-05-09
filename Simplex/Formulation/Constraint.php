<?php

namespace Simplex\Formulation;

abstract class Constraint
{
    protected Formula $formula;
    protected int $constraint;

    /**
     * GreaterOrEqual constructor.
     */
    public function __construct(Formula $formula, int $constraint)
    {
        $this->formula = $formula;
        $this->constraint = $constraint;
    }

    public function getFormula(): Formula
    {
        return $this->formula;
    }

    public function getConstraint(): int
    {
        return $this->constraint;
    }
}