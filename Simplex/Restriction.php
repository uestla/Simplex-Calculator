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

class Restriction extends VariableSet
{

    public const TYPE_EQ = 1;
    public const TYPE_LOE = 2;
    public const TYPE_GOE = 4;
    private int $type;

    private Fraction $limit;

    public function __construct(array $set, int $type, $limit)
    {
        parent::__construct($set);

        $this->type = (int)$type;
        $this->limit = Fraction::create($limit);
    }

    /** Deep copy */
    public function __clone()
    {
        $this->limit = clone $this->limit;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getLimit(): Fraction
    {
        return $this->limit;
    }

    public function fixRightSide(): Restriction
    {
        if ($this->limit->isLowerThan(0)) {
            $set = [];
            foreach ($this->set as $var => $coeff) {
                $set[$var] = $coeff->multiply(-1);
            }

            $type = $this->type === self::TYPE_EQ ? $this->type
                : ($this->type === self::TYPE_GOE ? self::TYPE_LOE : self::TYPE_GOE);

            $this->limit = $this->limit->multiply(-1);
        } else {
            $set = $this->set;
            $type = $this->type;
        }

        $this->set = $set;
        $this->type = $type;

        return $this;
    }

    public function getTypeSign(): string
    {
        return $this->type === self::TYPE_EQ ? '='
            : ($this->type === self::TYPE_LOE ? "\xe2\x89\xa4" : "\xe2\x89\xa5");
    }
}