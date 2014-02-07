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


class Task
{

	/** @var Func */
	private $function;

	/** @var Restriction[] */
	private $restrictions;

	/** @var array */
	private $basismap = array();



	/** @param  Func $function */
	function __construct(Func $function)
	{
		$this->function = $function;
	}



	/** @return Func */
	function getFunction()
	{
		return $this->function;
	}



	/**
	 * @param  Restriction $r
	 * @return Task
	 */
	function addRestriction(Restriction $r)
	{
		if ($r->getVariableList() !== $this->function->getVariableList()) {
			throw new \Exception;
		}

		$this->restrictions[] = $r;
		return $this;
	}



	/** @return Restriction[] */
	function getRestrictions()
	{
		return $this->restrictions;
	}



	/** @return Task */
	function fixRightSides()
	{
		$restrictions = $this->restrictions;
		$this->restrictions = array();

		foreach ($restrictions as $r) {
			$this->addRestriction($r->fixRightSide());
		}

		return $this;
	}



	/** @return Task */
	function fixNonEquations()
	{
		$newfunc = $this->function->getSet();

		$newrestr = array();
		foreach ($this->restrictions as $restriction) {
			$newrestr[] = array($restriction->getSet(), $restriction->getLimit());
		}

		$add = count($newfunc);
		$hlp = 0;

		foreach ($this->restrictions as $idx => $restriction) {
			$added = array();

			if ($restriction->getType() === Restriction::TYPE_EQ) {
				$hlpname = 'y' . ++$hlp;
				$added[$hlpname] = 1;
				$this->basismap[$hlpname] = $idx;

			} elseif ($restriction->getType() === Restriction::TYPE_LOE) {
				$addname = 'x' . ++$add;
				$added[$addname] = 1;
				$this->basismap[$addname] = $idx;

			} else {
				$hlpname = 'y' . ++$hlp;
				$added['x' . ++$add] = -1;
				$added[$hlpname] = 1;
				$this->basismap[$hlpname] = $idx;
			}

			foreach ($added as $newvar => $coeff) {
				$newfunc[$newvar] = 0;

				foreach ($this->restrictions as $i => $r) {
					$newrestr[$i][0][$newvar] = $idx === $i ? $coeff : 0;
				}
			}
		}

		ksort($this->basismap);
		$this->function = new Func($newfunc);

		$this->restrictions = array();
		foreach ($newrestr as $r) {
			$this->addRestriction(new Restriction($r[0], Restriction::TYPE_EQ, $r[1]));
		}

		return $this;
	}



	/** @return Table */
	function toTable()
	{
		$zcoeffs = array();
		foreach ($this->function->getSet() as $var => $coeff) {
			$zcoeffs[$var] = $coeff->multiply(-1);
		}

		$z = new Func($zcoeffs);

		$z2b = Fraction::create(0);
		$z2coeffs = array();

		foreach ($this->restrictions as $idx => $r) {
			foreach ($r->getSet() as $var => $coeff) {
				if (strncmp($var, 'y', 1) === 0 && $coeff->isEqualTo(1)) {
					foreach ($r->getSet() as $v => $c) {
						!isset($z2coeffs[$v]) && $z2coeffs[$v] = Fraction::create(0);
						strncmp($v, 'y', 1) !== 0 && ($z2coeffs[$v] = $z2coeffs[$v]->subtract($c));
					}

					$z2b = $z2b->subtract($r->getLimit());
				}
			}
		}

		$z2 = count($z2coeffs) ? new Func($z2coeffs, $z2b) : NULL;

		$table = new Table($z, $z2);
		foreach ($this->basismap as $var => $idx) {
			$table->addRow(
				new TableRow($var, $this->restrictions[$idx]->getSet(), $this->restrictions[$idx]->getLimit())
			);
		}

		return $table;
	}



	/** Deep copy */
	function __clone()
	{
		$this->function = clone $this->function;

		foreach ($this->restrictions as $key => $restriction) {
			$this->restrictions[$key] = clone $restriction;
		}
	}

}
