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

	/** @var array<string, Fraction>|null */
	private $alternativeSolution;


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


	/** @return array<string, Fraction>|null */
	public function getAlternativeSolution()
	{
		return $this->alternativeSolution;
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

		$this->solution = $this->extractSolution($tbl->getSolution());

		$altSolutionTbl = $tbl->getAlternativeSolution();

		if ($altSolutionTbl instanceof Table) {
			$this->steps[] = $altSolutionTbl;

			/** @var array<string, Fraction> $altSolution */
			$altSolution = $this->extractSolution($altSolutionTbl->getSolution());

			$this->alternativeSolution = $altSolution;
		}
	}


	/**
	 * @param  array<string, Fraction>|false|null $solution
	 * @return array<string, Fraction>|false|null
	 */
	private function extractSolution($solution)
	{
		if (!is_array($solution)) {
			return $solution;
		}

		$result = array();
		foreach ($this->task->getFunction()->getVariableList() as $var) {
			$result[$var] = $solution[$var];
		}

		return $result;
	}

}
