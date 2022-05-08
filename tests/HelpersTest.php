<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tester\Assert;
use Simplex\Helpers;

class HelpersTest extends TestCase
{
    public function test_isInt_helper(): void
    {
        $this->assertTrue(Helpers::isInt('0785'));
        $this->assertTrue(Helpers::isInt('-788'));
        $this->assertTrue(Helpers::isInt('-788e8'));
        $this->assertFalse(Helpers::isInt(NULL));
        $this->assertFalse(Helpers::isInt(TRUE));
        $this->assertFalse(Helpers::isInt(array()));
        $this->assertFalse(Helpers::isInt('a'));
    }

    public function test_sgn_helper(): void
    {
        $this->assertEquals(1, Helpers::sgn(7));
        $this->assertEquals(-1, Helpers::sgn(-7));
        $this->assertEquals(0, Helpers::sgn(0));
    }

    public function test_gcd_helper(): void
    {
        $this->assertEquals(3, Helpers::gcd(6, 27));
        $this->assertEquals(3, Helpers::gcd(-6, 27));
        $this->assertEquals(3, Helpers::gcd(6, -27));
        $this->assertEquals(3, Helpers::gcd(-6, -27));

        $this->assertEquals(1, Helpers::gcd(1, 24));
        $this->assertEquals(21, Helpers::gcd(0, 21));
        $this->assertEquals(256, Helpers::gcd(1400000000000000256, 100000000000000000));
    }

    public function test_gcd_at_least_one_number_must_not_be_a_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one number must not be a zero.');

        Helpers::gcd(0, 0);
    }
}
