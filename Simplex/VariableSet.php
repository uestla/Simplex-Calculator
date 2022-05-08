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

class VariableSet
{
    /** @var array */
    protected array $set;

    /** @param  array $set [ varname => fraction ] */
    public function __construct(array $set)
    {
        foreach ($set as $var => $coeff) {
            $set[$var] = Fraction::create($coeff);
        }

        ksort($set);
        $this->set = $set;
    }

    /** Deep copy */
    public function __clone()
    {
        foreach ($this->set as $var => $coeff) {
            $this->set[$var] = clone $coeff;
        }
    }

    /** @return array */
    public function getSet(): array
    {
        return $this->set;
    }

    /** @return Fraction|NULL */
    public function getMin(): ?Fraction
    {
        $min = null;

        foreach ($this->set as $var => $coeff) {
            if ($min === null || $coeff->isLowerThan($min)) {
                $min = $coeff;
            }
        }

        return $min;
    }

    /** @return array<string> */
    public function getVariableList(): array
    {
        return array_keys($this->set);
    }

    public function has(string $var): bool
    {
        return isset($this->set[$var]);
    }

    public function get(string $var): Fraction
    {
        return $this->set[$var];
    }
}
