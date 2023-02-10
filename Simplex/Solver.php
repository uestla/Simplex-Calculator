<?php

/**
 * This file is part of the SimplexCalculator library
 *
 * Copyright (c) 2014 Petr Kessler (https://kesspess.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Simplex-Calculator
 */

namespace Simplex;


class Solver
{

	/** @var array */
	private $steps = array();

	/** @var int */
	private $maxSteps;



	/**
	 * @param  Task $task
 	 * @param  int $maxSteps
	 */
	function __construct(Task $task, $maxSteps = 16)
	{
		$this->maxSteps = (int) $maxSteps;
		$this->steps[] = $task;

		$this->solve();
	}



	/** @return array */
	function getSteps()
	{
		return $this->steps;
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

			if (count($this->steps) > $this->maxSteps) {
				break ;
			}
		}

		if ($tbl->hasAlternativeSolution()) {
			$this->steps[] = $tbl->getAlternativeSolution();
		}
	}

}
