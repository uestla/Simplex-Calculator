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

class Fraction
{
    private ?int $n = null;

    private ?int $d = null;

    public function __construct(float $n, float $d = 1)
    {
        if (Helpers::isInt($n)) {
            $this->n = (int)$n;
        } else {
            $nf = self::fromDecimal($n);
            $this->n = $nf->getNumerator();
            $this->d = $nf->getDenominator();
        }

        if (Helpers::isInt($d)) {
            $this->d = (int)($d * ($this->d === null ? 1 : $this->d));
        } else {
            $df = self::fromDecimal($d);
            $this->n *= $df->getDenominator();
            $this->d = (int)($df->getNumerator() * ($this->d === null ? 1 : $this->d));
        }

        $this->canonicalize();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param Fraction|int|float $a
     * @return Fraction
     */
    public static function create($a): Fraction
    {
        if ($a instanceof self) {
            return $a;
        }

        return new self($a);
    }

    public function getNumerator(): int
    {
        return $this->n;
    }

    public function getDenominator(): int
    {
        return $this->d;
    }

    public function canonicalize(): Fraction
    {
        if ($this->d === 0) {
            throw new \Exception('Division by zero.');
        }

        if ($this->d < 0) {
            $this->n = -$this->n;
            $this->d = -$this->d;
        }

        $gcd = Helpers::gcd($this->n, $this->d);
        $this->n /= $gcd;
        $this->d /= $gcd;
        return $this;
    }

    /**
     * a/b + c/d = (ad + bc)/bd
     *
     * @param Fraction|int|float $a
     * @return Fraction
     */
    public function add($a): Fraction
    {
        $a = self::create($a);
        return new self($this->n * $a->getDenominator() + $this->d * $a->getNumerator(), $this->d * $a->getDenominator());
    }

    /**
     * a/b - c/d = (ad - bc)/bd
     * @param Fraction|int|float $a
     * @return Fraction
     */
    public function subtract($a): Fraction
    {
        return $this->add(self::create($a)->multiply(-1));
    }

    /**
     * (a/b)(c/d) = (ac/bd)
     * @param Fraction|int|float $a
     * @return Fraction
     */
    public function multiply($a): Fraction
    {
        $a = self::create($a);
        return new self($this->n * $a->getNumerator(), $this->d * $a->getDenominator());
    }

    /**
     * (a/b)/(c/d) = ad/bc
     * @param Fraction|int|float $a
     * @return Fraction
     */
    public function divide($a): Fraction
    {
        $a = self::create($a);
        return $this->multiply(new self($a->getDenominator(), $a->getNumerator()));
    }

    /** @return int -1, 0, 1 */
    public function sgn(): int
    {
        return Helpers::sgn($this->n);
    }

    public function absVal(): Fraction
    {
        return new self($this->sgn() * $this->n, $this->d);
    }

    /**
     * 2/3 vs 3/2 => 4/6 vs 9/6 => 4 < 9
     *
     * @param Fraction|int|float $a
     * @return int -1, 0, 1
     */
    public function compare($a): int
    {
        $a = self::create($a);
        return Helpers::sgn($this->n * $a->getDenominator() - $a->getNumerator() * $this->d);
    }

    public function isEqualTo($a): bool
    {
        return $this->compare($a) === 0;
    }

    public function isLowerThan($a): bool
    {
        return $this->compare($a) === -1;
    }

    public function isGreaterThan($a): bool
    {
        return $this->compare($a) === 1;
    }

    public function toString(): string
    {
        return $this->n . ($this->d !== 1 ? '/' . $this->d : '');
    }

    public function toFloat(): float
    {
        return $this->n / $this->d;
    }

    /**
     * 0.25 => 25/100
     */
    private static function fromDecimal(float $n): Fraction
    {
        if (Helpers::isInt($n)) {
            return new self($n);
        }

        $decpart = (float)($n - (int)$n);
        $mlp = pow(10, strlen((string)$decpart) - 2 - ($n < 0 ? 1 : 0));
        return new self((int)($n * $mlp), $mlp);
    }
}
