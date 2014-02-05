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


class Table
{

	/** @var TableRow[] */
	private $rows;

	/** @var TableRow */
	private $z;

	/** @var TableRow|NULL */
	private $z2;

	/** @var string[] */
	private $basis = array();

	/** @var array|bool */
	private $solution = NULL;

	/** @var array|bool */
	private $alternative = NULL;



	/**
	 * @param  Func $z
	 * @param  Func $z2
	 */
	function __construct(Func $z, Func $z2 = NULL)
	{
		$this->z = new TableRow('z', $z->getSet(), 0);
		$this->z2 = $z2 ? new TableRow('z\'', $z2->getSet(), $z2->getValue()) : NULL;
	}



	/** @return string[] */
	function getVariableList()
	{
		return $this->z->getVariableList();
	}



	/** @return TableRow */
	function getZ()
	{
		return $this->z;
	}



	/** @return TableRow|NULL */
	function getZ2()
	{
		return $this->z2;
	}



	/** @return bool */
	function hasZ2()
	{
		return $this->z2 !== NULL;
	}



	/**
	 * @param  TableRow $row
	 * @return Table
	 */
	function addRow(TableRow $row)
	{
		if ($row->getVariableList() !== $this->z->getVariableList()
				|| ($this->z2 !== NULL && $row->getVariableList() !== $this->z2->getVariableList())) {
			throw new \Exception;
		}

		$this->rows[] = $row;
		$this->basis[] = $row->getVar();
		return $this;
	}



	/** @return TableRow[] */
	function getRows()
	{
		return $this->rows;
	}



	/** @return bool */
	function hasHelperInBasis()
	{
		foreach ($this->rows as $row) {
			if (strncmp($row->getVar(), 'y', 1) === 0) {
				return TRUE;
			}
		}

		return FALSE;
	}



	/** @return bool */
	function isSolved()
	{
		if ($this->z2 !== NULL) {
			foreach ($this->z2->getSet() as $coeff) {
				if ($coeff->isLowerThan(0)) {
					return FALSE;
				}
			}

			if (!$this->z2->getB()->isEqualTo(0) || $this->hasHelperInBasis()) {
				$this->solution = FALSE;
				return TRUE;
			}
		}

		$keyval = $this->z->getMin();
		$keycol = array_search($keyval, $this->z->getSet(), TRUE);

		if ($keyval->isLowerThan(0)) {
			foreach ($this->rows as $row) {
				$set = $row->getSet();
				if ($set[$keycol]->isGreaterThan(0)) {
					return FALSE;
				}
			}

			$this->solution = FALSE;
		}

		return TRUE;
	}



	/** @return array|bool|NULL */
	function getSolution()
	{
		return $this->solution;
	}



	/** @return bool */
	function hasSolution()
	{
		return is_array($this->solution);
	}



	/** @return bool */
	function hasAlternativeSolution()
	{
		return $this->getAlternativeSolution() !== FALSE;
	}



	/** @return Table|bool */
	function getAlternativeSolution()
	{
		if ($this->alternative === NULL) {
			if (!$this->hasSolution()) {
				$this->alternative = FALSE;

			} else {
				foreach ($this->solution as $var => $value) {
					if ($value->isEqualTo(0) && !in_array($var, $this->basis, TRUE)) {
						$clone = unserialize(serialize($this));
						$solution = $clone->nextStep()->getSolution();

						foreach ($clone->nextStep()->getSolution() as $v => $val) {
							if (!$val->isEqualTo($this->solution[$v])) {
								$this->alternative = $clone;
								break 2;
							}
						}

						$this->alternative = FALSE;
						break;
					}
				}

				$this->alternative === NULL && ($this->alternative = FALSE);
			}
		}

		return $this->alternative;
	}



	/** @return string|NULL */
	function getKeyColumn()
	{
		$zrow = $this->hasHelperInBasis() ? 'z2' : 'z';

		$keycol = $keyval = NULL;
		foreach ($this->$zrow->getSet() as $var => $coeff) {
			if ($keyval === NULL || $coeff->isLowerThan($keyval)) {
				$keyval = $coeff;
				$keycol = $var;
			}
		}

		return $keycol;
	}



	/** @return TableRow|NULL */
	function getKeyRow()
	{
		$mint = $keyrow = NULL;
		$keycol = $this->getKeyColumn();

		if ($keycol === NULL) {
			return NULL;
		}

		foreach ($this->rows as $row) {
			$set = $row->getSet();
			if ($set[$keycol]->isGreaterThan(0)
					&& ($mint === NULL || $row->getB()->divide($set[$keycol])->isLowerThan($mint))) {
				$mint = $row->getB()->divide($set[$keycol]);
				$keyrow = $row;
			}
		}

		return $keyrow;
	}



	/** @return Table */
	function nextStep()
	{
		if ($this->z2 !== NULL && !$this->hasHelperInBasis()) {
			$this->removeHelpers();
		}

		$newrows = array();
		$keycol = $this->getKeyColumn();
		$keyrow = $this->getKeyRow();

		if ($keycol === NULL || $keyrow === NULL) {
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

		foreach (array('z', 'z2') as $zvar) {
			if ($this->$zvar === NULL) continue;

			$zcoeffs = array();
			$zdvd = $this->$zvar->get($keycol)->multiply(-1)->divide($keyrow->get($keycol));
			foreach ($this->$zvar->getSet() as $v => $c) {
				$zcoeffs[$v] = $zdvd->multiply($keyrow->get($v))->add($c);
			}

			$this->$zvar = new TableRow($zvar, $zcoeffs, $zdvd->multiply($keyrow->getB())->add($this->$zvar->getB()));
		}

		// find solution (if any)
		if ($this->isSolved()) {
			if ($this->solution !== FALSE) {
				$this->solution = array();

				foreach ($this->z->getVariableList() as $var) {
					if (strncmp($var, 'y', 1) === 0) continue;

					foreach ($this->rows as $row) {
						if ($row->getVar() === $var) {
							$this->solution[$var] = $row->getB();
							continue 2;
						}
					}

					$this->solution[$var] = Fraction::create(0);
				}

				ksort($this->solution);
			}
		}

		return $this;
	}



	/** @return void */
	private function removeHelpers()
	{
		$this->z2 = NULL;

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

}
