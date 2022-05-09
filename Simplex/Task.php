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

class Task
{
    private Func $function;

    /** @var array<Restriction> */
    private array $restrictions;

    /** @var array */
    private array $basismap = [];

    public function __construct(Func $function)
    {
        $this->function = $function;
    }

    /** Deep copy */
    public function __clone()
    {
        $this->function = clone $this->function;

        foreach ($this->restrictions as $key => $restriction) {
            $this->restrictions[$key] = clone $restriction;
        }
    }

    public function getFunction(): Func
    {
        return $this->function;
    }

    public function addRestriction(Restriction $r): Task
    {
        if ($r->getVariableList() !== $this->function->getVariableList()) {
            throw new \InvalidArgumentException("Restriction variables don't match the objective function variables.");
        }

        $this->restrictions[] = $r;
        return $this;
    }

    /** @return array<Restriction> */
    public function getRestrictions(): array
    {
        return $this->restrictions;
    }

    public function fixRightSides(): Task
    {
        $restrictions = $this->restrictions;
        $this->restrictions = [];

        foreach ($restrictions as $r) {
            $this->addRestriction($r->fixRightSide());
        }

        return $this;
    }

    public function fixNonEquations(): Task
    {
        $newfunc = $this->function->getSet();

        $newrestr = [];
        foreach ($this->restrictions as $restriction) {
            $newrestr[] = [$restriction->getSet(), $restriction->getLimit()];
        }

        $add = count($newfunc);
        $hlp = 0;

        foreach ($this->restrictions as $idx => $restriction) {
            $added = [];

            if ($restriction->getType() === Restriction::TYPE_EQ) {
                $hlpname = 'y' . ++$hlp;
                $added[$hlpname] = 1;
                $this->basismap[$hlpname] = $idx;
            } elseif ($restriction->getType() === Restriction::TYPE_LOE) {
                $addname = 'x' . ++$add;
                $added[$addname] = 1;
                $this->basismap[$addname] = $idx;
            } else {
                $hlpname = 'y' . ++$hlp;
                $added['x' . ++$add] = -1;
                $added[$hlpname] = 1;
                $this->basismap[$hlpname] = $idx;
            }

            foreach ($added as $newvar => $coeff) {
                $newfunc[$newvar] = 0;

                foreach ($this->restrictions as $i => $r) {
                    $newrestr[$i][0][$newvar] = $idx === $i ? $coeff : 0;
                }
            }
        }

        ksort($this->basismap);
        $this->function = new Func($newfunc);

        $this->restrictions = [];
        foreach ($newrestr as $r) {
            $this->addRestriction(new Restriction($r[0], Restriction::TYPE_EQ, $r[1]));
        }

        return $this;
    }

    public function toTable(): Table
    {
        $zcoeffs = [];
        foreach ($this->function->getSet() as $var => $coeff) {
            $zcoeffs[$var] = $coeff->multiply(-1);
        }

        $z = new ValueFunc($zcoeffs, 0);

        $z2b = Fraction::create(0);
        $z2coeffs = [];

        foreach ($this->restrictions as $idx => $r) {
            foreach ($r->getSet() as $var => $coeff) {
                if (strncmp($var, 'y', 1) === 0 && $coeff->isEqualTo(1)) {
                    foreach ($r->getSet() as $v => $c) {
                        ! isset($z2coeffs[$v]) && $z2coeffs[$v] = Fraction::create(0);
                        strncmp($v, 'y', 1) !== 0 && ($z2coeffs[$v] = $z2coeffs[$v]->subtract($c));
                    }

                    $z2b = $z2b->subtract($r->getLimit());
                }
            }
        }

        $z2 = count($z2coeffs) ? new ValueFunc($z2coeffs, $z2b) : null;

        $table = new Table($z, $z2);
        foreach ($this->basismap as $var => $idx) {
            $table->addRow(
                new TableRow($var, $this->restrictions[$idx]->getSet(), $this->restrictions[$idx]->getLimit())
            );
        }

        return $table;
    }
}
