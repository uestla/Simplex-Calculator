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


final class Table
{

	/** @var TableRow[] */
	private $rows;

	/** @var TableRow */
	private $z;

	/** @var TableRow|null */
	private $z2;

	/** @var string[] */
	private $basis = array();

	/** @var array<string, Fraction>|false */
	private $solution;

	/** @var self|bool */
	private $alternative;


	public function __construct(ValueFunc $z, ValueFunc $z2 = null)
	{
		if ($z2 !== null && $z2->getVariableList() !== $z->getVariableList()) {
			throw new \InvalidArgumentException("Variables of both objective functions don't match.");
		}

		$this->z = new TableRow('z', $z->getSet(), 0);
		$this->z2 = $z2 ? new TableRow('z\'', $z2->getSet(), $z2->getValue()) : null;
	}


	/** @return string[] */
	public function getVariableList()
	{
		return $this->z->getVariableList();
	}


	/** @return TableRow */
	public function getZ()
	{
		return $this->z;
	}


	/** @return TableRow|null */
	public function getZ2()
	{
		return $this->z2;
	}


	/** @return bool */
	public function hasZ2()
	{
		return $this->z2 !== null;
	}


	/** @return self */
	public function addRow(TableRow $row)
	{
		if ($row->getVariableList() !== $this->z->getVariableList()
				|| ($this->z2 !== null && $row->getVariableList() !== $this->z2->getVariableList())) {
			throw new \InvalidArgumentException("Row variables don't match the objective function variables.");
		}

		$this->rows[] = $row;
		$this->basis[] = $row->getVar();
		return $this;
	}


	/** @return TableRow[] */
	public function getRows()
	{
		return $this->rows;
	}


	/** @return bool */
	public function hasHelperInBasis()
	{
		foreach ($this->rows as $row) {
			if (strncmp($row->getVar(), 'y', 1) === 0) {
				return true;
			}
		}

		return false;
	}


	/** @return bool */
	public function isSolved()
	{
		if ($this->z2 !== null) {
			foreach ($this->z2->getSet() as $coeff) {
				if ($coeff->isLowerThan('0')) {
					return false;
				}
			}

			if ($this->hasHelperInBasis() || !$this->z2->getB()->isEqualTo('0')) {
				$this->solution = false;
				return true;
			}
		}

		$keyval = $this->z->getMin();

		if ($keyval === null) {
			$this->solution = false;
			return true;
		}

		$keycol = array_search($keyval, $this->z->getSet(), true);

		if ($keyval->isLowerThan('0')) {
			foreach ($this->rows as $row) {
				$set = $row->getSet();
				if ($set[$keycol]->isGreaterThan('0')) {
					return false;
				}
			}

			$this->solution = false;
		}

		return true;
	}


	/** @return array<string, Fraction>|false|null */
	public function getSolution()
	{
		return $this->solution;
	}


	/** @return bool */
	public function hasSolution()
	{
		return is_array($this->solution);
	}


	/** @return bool */
	public function hasAlternativeSolution()
	{
		return $this->getAlternativeSolution() !== false;
	}


	/** @return self|bool */
	public function getAlternativeSolution()
	{
		if ($this->alternative === null) {
			if (!is_array($this->solution)) {
				$this->alternative = false;

			} else {
				foreach ($this->solution as $var => $value) {
					if ($value->isEqualTo('0') && !in_array($var, $this->basis, true)) {
						$clone = clone $this;

						$nextStepSolution = $clone->nextStep()->getSolution();

						if (is_array($nextStepSolution)) {
							foreach ($nextStepSolution as $v => $val) {
								if (!$val->isEqualTo($this->solution[$v])) {
									$this->alternative = $clone;
									break 2;
								}
							}
						}

						$this->alternative = false;
						break;
					}
				}

				$this->alternative === null && ($this->alternative = false);
			}
		}

		return $this->alternative;
	}


	/** @return string|null */
	public function getKeyColumn()
	{
		$zrow = $this->hasHelperInBasis() ? 'z2' : 'z';

		$keycol = $keyval = null;
		foreach ($this->$zrow->getSet() as $var => $coeff) {
			if ($keyval === null || $coeff->isLowerThan($keyval)) {
				$keyval = $coeff;
				$keycol = $var;
			}
		}

		return $keycol;
	}


	/** @return TableRow|null */
	public function getKeyRow()
	{
		$mint = $keyrow = null;
		$keycol = $this->getKeyColumn();

		if ($keycol === null) {
			return null;
		}

		foreach ($this->rows as $row) {
			$set = $row->getSet();
			if ($set[$keycol]->isGreaterThan('0')
					&& ($mint === null || $row->getB()->divide($set[$keycol])->isLowerThan($mint))) {
				$mint = $row->getB()->divide($set[$keycol]);
				$keyrow = $row;
			}
		}

		return $keyrow;
	}


	/** @return self */
	public function nextStep()
	{
		if ($this->z2 !== null && !$this->hasHelperInBasis()) {
			$this->removeHelpers();
		}

		$newrows = array();
		$keycol = $this->getKeyColumn();
		$keyrow = $this->getKeyRow();

		if ($keycol === null || $keyrow === null) {
			return $this;
		}

		foreach ($this->rows as $row) {
			$rowset = array();

			if ($row->getVar() === $keyrow->getVar()) {
				$var = $keycol;
				$b = $row->getB()->divide($keyrow->get($keycol));

				foreach ($row->getSet() as $v => $c) {
					$rowset[$v] = $c->divide($keyrow->get($keycol));
				}

			} else {
				$var = $row->getVar();
				$set = $row->getSet();
				$dvd = $set[$keycol]->multiply(-1)->divide($keyrow->get($keycol));
				$b = $dvd->multiply($keyrow->getB())->add($row->getB());

				foreach ($row->getSet() as $v => $c) {
					$rowset[$v] = $dvd->multiply($keyrow->get($v))->add($c);
				}
			}

			$newrows[$var] = array($rowset, $b);
		}

		$this->rows = array();
		foreach ($newrows as $var => $meta) {
			$this->addRow(new TableRow($var, $meta[0], $meta[1]));
		}

		$this->z = $this->transformZFunction($this->z, $keycol, $keyrow);

		if ($this->z2 !== null) {
			$this->z2 = $this->transformZFunction($this->z2, $keycol, $keyrow);
		}

		// find solution (if any)
		if ($this->isSolved()) {
			if ($this->solution !== false) {
				$this->solution = array();

				foreach ($this->z->getVariableList() as $var) {
					if (strncmp($var, 'y', 1) === 0) {
						continue;
					}

					$this->solution[$var] = isset($newrows[$var][1]) ? $newrows[$var][1] : new Fraction('0');
				}

				ksort($this->solution);
			}
		}

		return $this;
	}


	/**
	 * @param  string $keycol
	 * @return TableRow
	 */
	private function transformZFunction(TableRow $z, $keycol, TableRow $keyrow)
	{
		$coeffs = array();
		$dvd = $z->get($keycol)->multiply('-1')->divide($keyrow->get($keycol));

		foreach ($z->getSet() as $v => $c) {
			$coeffs[$v] = $dvd->multiply($keyrow->get($v))->add($c);
		}

		return new TableRow($z->getVar(), $coeffs, $dvd->multiply($keyrow->getB())->add($z->getB()));
	}


	/** @return void */
	private function removeHelpers()
	{
		$this->z2 = null;

		$newrows = array();
		foreach ($this->rows as $row) {
			$newrow = array();
			foreach ($row->getSet() as $v => $c) {
				if ($v[0] !== 'y') {
					$newrow[$v] = $c;
				}
			}

			$newrows[] = new TableRow($row->getVar(), $newrow, $row->getB());
		}

		$this->rows = $newrows;

		$newz = array();
		foreach ($this->z->getSet() as $var => $coeff) {
			if ($var[0] !== 'y') {
				$newz[$var] = $coeff;
			}
		}

		$this->z = new TableRow($this->z->getVar(), $newz, $this->z->getB());
	}


	/** Deep copy */
	public function __clone()
	{
		foreach ($this->rows as $key => $row) {
			$this->rows[$key] = clone $row;
		}

		$this->z = clone $this->z;

		if (is_object($this->z2)) {
			$this->z2 = clone $this->z2;
		}
	}

}
