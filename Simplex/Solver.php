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

	/** @var array<string, Fraction>|false|null */
	private $solution;

	/** @var array<int, array<string, Fraction>>|null */
	private $alternativeSolutions;


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
		return $this->solution;
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
		return $this->alternativeSolutions;
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

		$this->solution = $tbl->getSolution();

		$altSolutionTbl = $tbl->getAlternativeSolution();

		if ($altSolutionTbl instanceof Table) {
			$this->steps[] = $altSolutionTbl;

			$altSolution = $altSolutionTbl->getSolution();

			if (is_array($altSolution)) {
				$this->alternativeSolutions = array($altSolution);
			}

		} elseif ($tbl->hasSolution()) {
			$this->alternativeSolutions = array();
		}
	}

}
