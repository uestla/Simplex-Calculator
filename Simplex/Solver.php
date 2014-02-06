<?php

/**
 * This file is part of the SimplexCalculator library
 *
 * Copyright (c) 2014 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Dual-Simplex-Calculator
 */

namespace Simplex;


class Solver
{

	/** @var array */
	private $steps = array();



	const MAX_STEPS = 16;



	/** @param  Task $task */
	function __construct(Task $task)
	{
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
		$t = unserialize(serialize(reset($this->steps)));
		$this->steps[] = $t->fixRightSides();

		$t = unserialize(serialize($t));
		$this->steps[] = $t->fixNonEquations();

		$this->steps[] = $tbl = $t->toTable();
		while (!$tbl->isSolved()) {
			$tbl = unserialize(serialize($tbl));
			$this->steps[] = $tbl->nextStep();

			if (count($this->steps) > self::MAX_STEPS) {
				break ;
			}
		}

		if ($tbl->hasAlternativeSolution()) {
			$this->steps[] = $tbl->getAlternativeSolution();
		}
	}

}
