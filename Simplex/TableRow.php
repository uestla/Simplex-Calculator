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

class TableRow extends VariableSet
{
    private string $var;

    private Fraction $b;

    /**
     * @param array $set
     */
    public function __construct(string $var, array $set, $b)
    {
        parent::__construct($set);

        $this->var = (string)$var;
        $this->b = Fraction::create($b);
    }

    /** Deep copy */
    public function __clone()
    {
        parent::__clone();

        $this->b = clone $this->b;
    }

    public function getVar(): string
    {
        return $this->var;
    }

    public function getB(): Fraction
    {
        return $this->b;
    }
}
