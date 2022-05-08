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

class Table
{
    /** @var array<TableRow> */
    private array $rows;

    private TableRow $z;

    /** @var TableRow|NULL */
    private ?TableRow $z2 = null;

    /** @var array<string> */
    private array $basis = [];

    // TODO: make it nullable, not boolean
    private $solution = null;

    private $alternative = null;

    public function __construct(ValueFunc $z, ?ValueFunc $z2 = null)
    {
        if ($z2 !== null && $z2->getVariableList() !== $z->getVariableList()) {
            throw new \InvalidArgumentException("Variables of both objective functions don't match.");
        }

        $this->z = new TableRow('z', $z->getSet(), 0);
        $this->z2 = $z2 ? new TableRow('z\'', $z2->getSet(), $z2->getValue()) : null;
    }

    /** Deep copy */
    public function __clone()
    {
        foreach ($this->rows as $key => $row) {
            $this->rows[$key] = clone $row;
        }

        $this->z = clone $this->z;

        if (is_object($this->z2)) {
            $this->z2 = clone $this->z2;
        }
    }

    /** @return array<string> */
    public function getVariableList(): array
    {
        return $this->z->getVariableList();
    }

    public function getZ(): TableRow
    {
        return $this->z;
    }

    /** @return TableRow|NULL */
    public function getZ2(): ?TableRow
    {
        return $this->z2;
    }

    public function hasZ2(): bool
    {
        return $this->z2 !== null;
    }

    public function addRow(TableRow $row): Table
    {
        if ($row->getVariableList() !== $this->z->getVariableList()
            || ($this->z2 !== null && $row->getVariableList() !== $this->z2->getVariableList())) {
            throw new \InvalidArgumentException("Row variables don't match the objective function variables.");
        }

        $this->rows[] = $row;
        $this->basis[] = $row->getVar();
        return $this;
    }

    /** @return array<TableRow> */
    public function getRows(): array
    {
        return $this->rows;
    }

    public function hasHelperInBasis(): bool
    {
        foreach ($this->rows as $row) {
            if (strncmp($row->getVar(), 'y', 1) === 0) {
                return true;
            }
        }

        return false;
    }

    public function isSolved(): bool
    {
        if ($this->z2 !== null) {
            foreach ($this->z2->getSet() as $coeff) {
                if ($coeff->isLowerThan(0)) {
                    return false;
                }
            }

            if (!$this->z2->getB()->isEqualTo(0) || $this->hasHelperInBasis()) {
                $this->solution = false;
                return true;
            }
        }

        $keyval = $this->z->getMin();
        $keycol = array_search($keyval, $this->z->getSet(), true);

        if ($keyval->isLowerThan(0)) {
            foreach ($this->rows as $row) {
                $set = $row->getSet();
                if ($set[$keycol]->isGreaterThan(0)) {
                    return false;
                }
            }

            $this->solution = false;
        }

        return true;
    }

    /** @return array|bool|NULL */
    public function getSolution()
    {
        return $this->solution;
    }

    public function hasSolution(): bool
    {
        return is_array($this->solution);
    }

    public function hasAlternativeSolution(): bool
    {
        return $this->getAlternativeSolution() !== false;
    }

    public function getAlternativeSolution()
    {
        if ($this->alternative === null) {
            if (!$this->hasSolution()) {
                $this->alternative = false;
            } else {
                foreach ($this->solution as $var => $value) {
                    if ($value->isEqualTo(0) && !in_array($var, $this->basis, true)) {
                        $clone = clone $this;
                        $solution = $clone->nextStep()->getSolution();

                        foreach ($clone->nextStep()->getSolution() as $v => $val) {
                            if (!$val->isEqualTo($this->solution[$v])) {
                                $this->alternative = $clone;
                                break 2;
                            }
                        }

                        $this->alternative = false;
                        break;
                    }
                }

                $this->alternative === null && ($this->alternative = false);
            }
        }

        return $this->alternative;
    }

    /** @return string|NULL */
    public function getKeyColumn(): ?string
    {
        $zrow = $this->hasHelperInBasis() ? 'z2' : 'z';

        $keycol = $keyval = null;
        foreach ($this->$zrow->getSet() as $var => $coeff) {
            if ($keyval === null || $coeff->isLowerThan($keyval)) {
                $keyval = $coeff;
                $keycol = $var;
            }
        }

        return $keycol;
    }

    /** @return TableRow|NULL */
    public function getKeyRow(): ?TableRow
    {
        $mint = $keyrow = null;
        $keycol = $this->getKeyColumn();

        if ($keycol === null) {
            return null;
        }

        foreach ($this->rows as $row) {
            $set = $row->getSet();
            if ($set[$keycol]->isGreaterThan(0)
                && ($mint === null || $row->getB()->divide($set[$keycol])->isLowerThan($mint))) {
                $mint = $row->getB()->divide($set[$keycol]);
                $keyrow = $row;
            }
        }

        return $keyrow;
    }

    public function nextStep(): Table
    {
        if ($this->z2 !== null && !$this->hasHelperInBasis()) {
            $this->removeHelpers();
        }

        $newrows = [];
        $keycol = $this->getKeyColumn();
        $keyrow = $this->getKeyRow();

        if ($keycol === null || $keyrow === null) {
            return $this;
        }

        foreach ($this->rows as $row) {
            $rowset = [];

            if ($row->getVar() === $keyrow->getVar()) {
                $var = $keycol;
                $b = $row->getB()->divide($keyrow->get($keycol));

                foreach ($row->getSet() as $v => $c) {
                    $rowset[$v] = $c->divide($keyrow->get($keycol));
                }
            } else {
                $var = $row->getVar();
                $set = $row->getSet();
                $dvd = $set[$keycol]->multiply(-1)->divide($keyrow->get($keycol));
                $b = $dvd->multiply($keyrow->getB())->add($row->getB());

                foreach ($row->getSet() as $v => $c) {
                    $rowset[$v] = $dvd->multiply($keyrow->get($v))->add($c);
                }
            }

            $newrows[$var] = [$rowset, $b];
        }

        $this->rows = [];
        foreach ($newrows as $var => $meta) {
            $this->addRow(new TableRow($var, $meta[0], $meta[1]));
        }

        foreach (['z', 'z2'] as $zvar) {
            if ($this->$zvar === null) {
                continue;
            }

            $zcoeffs = [];
            $zdvd = $this->$zvar->get($keycol)->multiply(-1)->divide($keyrow->get($keycol));
            foreach ($this->$zvar->getSet() as $v => $c) {
                $zcoeffs[$v] = $zdvd->multiply($keyrow->get($v))->add($c);
            }

            $this->$zvar = new TableRow($zvar, $zcoeffs, $zdvd->multiply($keyrow->getB())->add($this->$zvar->getB()));
        }

        // find solution (if any)
        if ($this->isSolved() && $this->solution !== false) {
            $this->solution = [];

            foreach ($this->z->getVariableList() as $var) {
                if (strncmp($var, 'y', 1) === 0) {
                    continue;
                }

                foreach ($this->rows as $row) {
                    if ($row->getVar() === $var) {
                        $this->solution[$var] = $row->getB();
                        continue 2;
                    }
                }

                $this->solution[$var] = Fraction::create(0);
            }

            ksort($this->solution);
        }

        return $this;
    }

    private function removeHelpers(): void
    {
        $this->z2 = null;

        $newrows = [];
        foreach ($this->rows as $row) {
            $newrow = [];
            foreach ($row->getSet() as $v => $c) {
                if ($v[0] !== 'y') {
                    $newrow[$v] = $c;
                }
            }

            $newrows[] = new TableRow($row->getVar(), $newrow, $row->getB());
        }

        $this->rows = $newrows;

        $newz = [];
        foreach ($this->z->getSet() as $var => $coeff) {
            if ($var[0] !== 'y') {
                $newz[$var] = $coeff;
            }
        }

        $this->z = new TableRow($this->z->getVar(), $newz, $this->z->getB());
    }
}
