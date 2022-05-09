<?php

namespace Tests;

use Simplex\Func;
use Simplex\Restriction;
use Simplex\Task;

trait TaskCreator
{
    /**
     * @return Task
     */
    private function getFirstTask(): Task
    {
        $z = new Func(array(
            'x1' => 2,
            'x2' => 1,
        ));

        $task = new Task($z);

        $task->addRestriction(new Restriction(array(
            'x1' => 1,
            'x2' => 1,

        ), Restriction::TYPE_GOE, 2));

        $task->addRestriction(new Restriction(array(
            'x1' => 1,
            'x2' => -1,

        ), Restriction::TYPE_EQ, 0));

        $task->addRestriction(new Restriction(array(
            'x1' => 1,
            'x2' => -2,

        ), Restriction::TYPE_GOE, -4));

        return $task;
    }
}