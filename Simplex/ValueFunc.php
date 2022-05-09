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

class ValueFunc extends VariableSet
{
    private Fraction $value;

    /**
     * @param array $set
     */
    public function __construct(array $set, $value)
    {
        parent::__construct($set);

        $this->value = Fraction::create($value);
    }

    /** Deep copy */
    public function __clone()
    {
        parent::__clone();

        if (is_object($this->value)) {
            $this->value = clone $this->value;
        }
    }

    public function getValue(): Fraction
    {
        return $this->value;
    }
}
