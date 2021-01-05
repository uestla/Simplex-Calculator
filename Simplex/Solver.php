<?php

/**
 * This file is part of the SimplexCalculator library
 *
 * Copyright (c) 2014 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Simplex-Calculator
 */

namespace Simplex;


class Solver
{

	/** @var array */
	private $steps = array();
	private $max_steps = 16;



	/** @param  Task $task */
	/** @param int $maxSteps (optional) */
	function __construct(Task $task, int $maxSteps = null)
	{
		if ($maxSteps !== null) {
			$this->max_steps = $maxSteps;
		}

		$this->steps[] = $task;
		$this->solve();
	}



	/** @return array */
	function getSteps()
	{
		return $this->steps;
	}



	/** @return int */
	function getMaxSteps()
	{
		return $this->max_steps;
	}



	/** @return void */
	private function solve()
	{
		$t = clone reset($this->steps);
		$this->steps[] = $t->fixRightSides();

		$t = clone $t;
		$this->steps[] = $t->fixNonEquations();

		$this->steps[] = $tbl = $t->toTable();
		while (!$tbl->isSolved()) {
			$tbl = clone $tbl;
			$this->steps[] = $tbl->nextStep();

			if (count($this->steps) > $this->getMaxSteps()) {
				break ;
			}
		}

		if ($tbl->hasAlternativeSolution()) {
			$this->steps[] = $tbl->getAlternativeSolution();
		}
	}

}
