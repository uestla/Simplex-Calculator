<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Simplex\Table;
use Simplex\TableRow;
use Simplex\ValueFunc;

class TableTest extends TestCase
{
    public function test_variables_dont_match(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Variables of both objective functions don't match.");

        $z = new ValueFunc(array(
            'x1' => 5,
        ), 13);

        $z2 = new ValueFunc(array(
            'x2' => 7,
        ), 43);

        new Table($z, $z2);
    }


    public function test_row_variables_dont_match_objective_function_variables(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Row variables don't match the objective function variables.");

        $t = new Table(new ValueFunc(array(
            'x1' => 4,
        ), 42));

        $t->addRow(new TableRow('x1', array(
            'x2' => 5,
        ), 14));
    }
}
