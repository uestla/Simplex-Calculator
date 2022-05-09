<?php

namespace Simplex\Formulation;

/**
 * Class Formula
 *
 * Formula class is used for write formula of a function.
 *
 * For example:
 * y = 10x1 + 2x2 - x3 + 3x5
 *
 * Then instantiation should be:
 * new Formula(10, 2, -1, 0, 3)
 *
 * @package Simplex\Formalization
 */
class Formula
{
    private array $coefficients;

    /**
     * Formula constructor.
     */
    public function __construct(...$coefficients)
    {
        if (is_array($coefficients[0])) {
            $coefficients = $coefficients[0];
        }

        // not keys in case of assoc
        $i = 0;

        foreach ($coefficients as $coefficient) {
            if (!is_numeric($coefficient)) {
                throw new \InvalidArgumentException("Construct \$coefficient params have to be number");
            }

            $this->coefficients['x' . ++$i] = $coefficient;
        }
    }

    public function getCoefficients(): array
    {
        return $this->coefficients;
    }

    public function getSize(): int
    {
        return count($this->coefficients);
    }

    public function getParam(string $variable): float
    {
        if (is_numeric($variable)) {
            $variable = 'x' . $variable;
        }

        if (!array_key_exists($variable, $this->coefficients)) {
            throw new \InvalidArgumentException('Variable ' . $variable . 'is not exists');
        }

        return $this->coefficients[$variable];
    }
}