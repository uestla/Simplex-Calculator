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


final class Solver
{

	/** @var array */
	private $steps = array();

	/** @var int */
	private $maxSteps;


	/**
	 * @param  Task $task
 	 * @param  int $maxSteps
	 */
	public function __construct(Task $task, $maxSteps = 16)
	{
		$this->maxSteps = (int) $maxSteps;
		$this->steps[] = $task;

		$this->solve();
	}


	/** @return array */
	public function getSteps()
	{
		return $this->steps;
	}


	/** @return array|bool|null */
	public function getSolution()
	{
		// find first table with a solution in steps
		foreach ($this->steps as $step) {
			if (!$step instanceof Table) {
				continue ;
			}

			if ($step->isSolved()) {
				return $step->getSolution();
			}
		}

		return null;
	}


	/** @return array|bool|null */
	public function getAlternativeSolutions()
	{
		$first = false;
		$alternatives = array();

		foreach ($this->steps as $step) {
			if (!$step instanceof Table) {
				continue ;
			}

			if ($step->isSolved()) {
				if (!$first) {
					$first = true;
					continue ;
				}

				$alternatives[] = $step->getSolution();
			}
		}

		return $first ? $alternatives : null;
	}


	/** @return void */
	private function solve()
	{
		/** @var Task $t */
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
