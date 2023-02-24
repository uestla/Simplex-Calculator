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

	/** @var Task */
	private $task;

	/** @var Task */
	private $fixedRightSide;

	/** @var Task */
	private $fixedNonEquations;

	/** @var Table[] */
	private $steps = array();

	/** @var int */
	private $maxSteps;


	/** @param  int $maxSteps */
	public function __construct(Task $task, $maxSteps = 16)
	{
		$this->maxSteps = (int) $maxSteps;
		$this->solve($task);
	}


	/** @return Task */
	public function getTask()
	{
		return $this->task;
	}


	/** @return Task */
	public function getFixedRightSide()
	{
		return $this->fixedRightSide;
	}


	/** @return Task */
	public function getFixedNonEquations()
	{
		return $this->fixedNonEquations;
	}


	/** @return Table[] */
	public function getSteps()
	{
		return $this->steps;
	}


	/** @return array<string, Fraction>|false|null */
	public function getSolution()
	{
		// find first table with a solution in steps
		foreach ($this->steps as $step) {
			if ($step->isSolved()) {
				return $step->getSolution();
			}
		}

		return null;
	}


	/**
	 * @param  array<string, Fraction> $solution
	 * @return Fraction
	 */
	public function getSolutionValue(array $solution)
	{
		$value = new Fraction('0');
		foreach ($this->task->getFunction()->getSet() as $var => $coeff) {
			if (!isset($solution[$var])) {
				continue ;
			}

			$value = $value->add($coeff->multiply($solution[$var]));
		}

		return $value;
	}


	/** @return array<int, array<string, Fraction>>|false|null */
	public function getAlternativeSolutions()
	{
		$first = false;
		$alternatives = array();

		foreach ($this->steps as $step) {
			if ($step->isSolved()) {
				if (!$first) {
					$first = true;
					continue ;
				}

				$altSolution = $step->getSolution();

				if (is_array($altSolution)) {
					$alternatives[] = $altSolution;
				}
			}
		}

		return $first ? $alternatives : null;
	}


	/** @return void */
	private function solve(Task $task)
	{
		$this->task = $task;

		$t = clone $task;
		$this->fixedRightSide = $t->fixRightSides();

		$t = clone $t;
		$this->fixedNonEquations = $t->fixNonEquations();

		$this->steps[] = $tbl = $t->toTable();

		while (!$tbl->isSolved()) {
			$tbl = clone $tbl;
			$this->steps[] = $tbl->nextStep();

			if (count($this->steps) >= $this->maxSteps) {
				break ;
			}
		}

		$altSolutionTbl = $tbl->getAlternativeSolution();

		if ($altSolutionTbl instanceof Table) {
			$this->steps[] = $altSolutionTbl;
		}
	}

}
