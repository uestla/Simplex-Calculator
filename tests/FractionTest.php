<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Simplex\Fraction;

class FractionTest extends TestCase
{
    private Fraction $a;
    private Fraction $b;

    protected function setUp(): void
    {
        parent::setUp();
        $this->a = new Fraction(1, 4);
        $this->b = new Fraction(2, 3);
    }

    public function test_divaded_by_zero(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Division by zero.');

        new Fraction(3, 0);
    }

    public function fractionEqualizationDataProvider(): array
    {
        return [
            [0.25, '1/4'],
            [-0.25, '-1/4'],
            [-0.25, '1/4', -1],
            [8, '32', 1 / 4],
            [0.14, '7/50']
        ];
    }

    /**
     * @dataProvider fractionEqualizationDataProvider
     */
    public function test_fraction_equalization(float $nominator, string $expectation, float $denominator = 1): void
    {
        $f = new Fraction($nominator, $denominator);
        $this->assertEquals($expectation, (string)$f);
    }

    public function test_fraction_adding(): void
    {
        $this->assertEquals('11/12', (string)$this->a->add($this->b));
        $this->assertEquals('11/12', (string)$this->b->add($this->a));
    }

    public function test_fraction_multiplying(): void
    {
        $this->assertEquals('1/6', (string)$this->a->multiply($this->b));
        $this->assertEquals('1/6', (string)$this->b->multiply($this->a));
    }

    public function test_fraction_dividing(): void
    {
        $this->assertEquals('3/8', (string)$this->a->divide($this->b));
        $this->assertEquals('8/3', (string)$this->b->divide($this->a));
    }

    public function test_fraction_constants_multiply(): void
    {
        $a = new Fraction(1 / 4);
        $this->assertEquals('-1/4', (string)$a->multiply(-1));
    }

    public function test_fraction_sng_abs_value(): void
    {
        $a = new Fraction(-1 / 4);
        $this->assertEquals(-1, $a->sgn());
        $this->assertEquals('1/4', (string)$a->absVal());
    }

    public function test_fraction_comparison(): void
    {
        $a = new Fraction(2, 3);
        $b = new Fraction(3, 2);

        $this->assertTrue($a->isLowerThan($b));
        $this->assertTrue($b->isGreaterThan($a));
        $this->assertFalse($a->isLowerThan($a));
        $this->assertFalse($a->isGreaterThan($a));
    }
}