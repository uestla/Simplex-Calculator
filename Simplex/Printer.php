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


final class Printer
{

	/** @return void */
	public function printSolver(Solver $solver)
	{
		echo 'TASK ----------------------', "\n\n";

		$this->printTask($solver->getTask());
		echo "\n";

		echo "SOLUTION STEPS ------------", "\n\n";

		echo '1. Standard task transformation', "\n";
		$solver->getFixedRightSide();
		$this->printTask($solver->getFixedNonEquations());
		echo "\n";

		echo '2. Simplex tableaus', "\n\n";
		foreach ($solver->getSteps() as $key => $step) {
			echo 'Tableau #', $key + 1, "\n";

			$this->printTable($step);
			echo "\n\n";
		}

		echo 'OPTIMAL SOLUTION ----------', "\n";
		$this->printSolution($solver);
	}


	/** @return void */
	public function printTask(Task $task)
	{
		echo 'Maximize:', "\n";
		echo '  ';
		$this->printFunction($task->getFunction());

		echo "\n";
		echo 'subject to:', "\n";
		foreach ($task->getRestrictions() as $restriction) {
			echo '  ';
			$this->printRestriction($restriction);
			echo "\n";
		}
	}


	/** @return void */
	public function printTable(Table $table)
	{
		$header = $this->buildTableHeader($table);
		$body = $this->buildTableBody($table);
		$footer = $this->buildTableFooter($table);

		$parts = array(array($header), $body, $footer);

		// calculate column widths
		$colWidths = array();

		foreach ($parts as $part) {
			foreach ($part as $row) {
				foreach ($row as $col => $value) {
					$len = strlen($value);

					if (!isset($colWidths[$col]) || $len > $colWidths[$col]) {
						$colWidths[$col] = $len;
					}
				}
			}
		}

		$rightCol = count($header) - ($table->hasSolution() ? 2 : 3);

		$border = '';
		foreach ($colWidths as $col => $width) {
			$border .= str_repeat('-', $width + 2);
			$border .= '+' . ($col === $rightCol ? '+' : '');
		}

		foreach ($parts as $key => $part) {
			if ($key === 1 || $key === 2) {
				echo $border, "\n";
			}

			foreach ($part as $row) {
				foreach ($row as $col => $value) {
					echo ' ';
					echo str_pad($value, $colWidths[$col], ' ', STR_PAD_LEFT);
					echo ' |';
					echo $col === $rightCol ? '|' : '';
				}

				echo "\n";
			}
		}
	}


	/** @return string[] */
	private function buildTableHeader(Table $table)
	{
		$header = $table->getVariableList();
		array_unshift($header, '');
		$header[] = 'b';

		if (!$table->hasSolution()) {
			$header[] = 't';
		}

		return $header;
	}


	/** @return array<int, string[]> */
	private function buildTableBody(Table $table)
	{
		// body
		$body = array();
		foreach ($table->getRows() as $tableRow) {
			$bodyRow = array_values($tableRow->getSet());
			array_unshift($bodyRow, $tableRow->getVar());
			$bodyRow[] = $tableRow->getB();

			if (!$table->hasSolution()) {
				$keyCol = $table->getKeyColumn();

				if ($keyCol === null) {
					$bodyRow[] = '-';

				} else {
					$keyColValue = $tableRow->get($keyCol);

					if ($keyColValue->isGreaterThan('0')) {
						$bodyRow[] = $tableRow->getB()->divide($keyColValue);

					} else {
						$bodyRow[] = '-';
					}
				}
			}

			$body[] = $bodyRow;
		}

		return $body;
	}


	/** @return array<int, string[]> */
	private function buildTableFooter(Table $table)
	{
		$footer = array();

		$zRow = array_values($table->getZ()->getSet());
		array_unshift($zRow, 'z');
		$zRow[] = $table->getZ()->getB();

		if (!$table->hasSolution()) {
			$zRow[] = '';
		}

		$footer[] = $zRow;

		$z2 = $table->getZ2();

		if ($z2 !== null) {
			$z2Row = array_values($z2->getSet());
			array_unshift($z2Row, 'z2');
			$z2Row[] = $z2->getB();

			if (!$table->hasSolution()) {
				$z2Row[] = '';
			}

			$footer[] = $z2Row;
		}

		return $footer;
	}


	/** @return void */
	public function printSolution(Solver $solver)
	{
		$solution = $solver->getSolution();

		if ($solution === false) {
			echo 'Solution not found.', "\n";

		} elseif ($solution === null) {
			echo 'Maximum number of ', count($solver->getSteps()) - 1, ' steps reached.', "\n";

		} else {
			$altSolution = $solver->getAlternativeSolution();

			if ($altSolution === null) {
				$this->printVector(array_values($solution));

			} else {
				echo 'λ';
				$this->printVector(array_values($solution));
				echo ' + (1 - λ)';
				$this->printVector(array_values($altSolution));
			}

			echo ' = ', $solver->getSolutionValue($solution), "\n";
		}
	}


	/** @return void */
	public function printFunction(Func $function)
	{
		$this->printVariableSet($function);
		echo "\n";
	}


	/** @return void */
	public function printRestriction(Restriction $restriction)
	{
		$this->printVariableSet($restriction);
		echo ' ', $restriction->getTypeSign(), ' ';
		$this->printFraction($restriction->getLimit(), false, false);
	}


	/**
	 * @param  Fraction[] $coeffs
	 * @return void
	 */
	public function printVector(array $coeffs)
	{
		echo '<', implode(', ', $coeffs), '>';
	}


	/** @return void */
	public function printVariableSet(VariableSet $vars)
	{
		$first = true;
		$set = $vars->getSet();

		foreach ($set as $var => $coeff) {
			if (!$first) {
				echo ' ';
			}

			$this->printFraction($coeff, !$first, true);
			echo $var;

			$first = false;
		}
	}


	/**
	 * @param  bool $forceSign
	 * @param  bool $omitOne
	 * @return void
	 */
	public function printFraction(Fraction $fraction, $forceSign, $omitOne)
	{
		$isNeg = $fraction->isLowerThan('0');

		if ($forceSign) {
			echo $isNeg ? '-' : '+', ' ';

		} elseif ($isNeg) {
			echo '-';
		}

		$abs = $fraction->absVal();

		if ($abs->getDenominator() === '1') {
			if (!$omitOne || $abs->getNumerator() !== '1') {
				echo $abs->getNumerator();
			}

		} else {
			echo '(', $abs->getNumerator(), '/', $abs->getDenominator(), ')';
		}
	}

}
