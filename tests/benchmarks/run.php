<?php

declare(strict_types = 1);

namespace Simplex\Tests\Benchmarks;


require_once __DIR__ . '/../../src/Simplex/Math/math.php';
require_once __DIR__ . '/negations.php';
require_once __DIR__ . '/fractions.php';


// =============================================================================
// === STRING MANIPULATION =====================================================
// =============================================================================

(static function (): void {

	echo "\n";

	echo '> String Manipulation Benchmarks', "\n\n";

	$cases = [
		'Remove "." - str_replace()' => static fn(string $n, int $dotPos): string => str_replace('.', '', $n),
		'Remove "." - substr()' => static fn(string $n, int $dotPos): string => str_replace('.', '', $n),
	];

	$caseNo = 0;
	$steps = 1e8;

	foreach ($cases as $title => $callback) {
		echo '  ', ++$caseNo, '. ', $title, '...';

		$t = hrtime(true);

		$n = '1234567890.0987654321';
		for ($i = 0; $i < $steps; $i++) {
			$res = $callback($n, 10);
		}

		assert(($res ?? '') === '12345678900987654321');

		$t = hrtime(true) - $t;
		echo ' ', $t / 1e9, 's', "\n";
	}

})();


// =============================================================================
// === NEGATION ================================================================
// =============================================================================

(static function (): void {

	echo "\n";

	echo '> Negation Benchmarks', "\n\n";

	$cases = [
		'BCMath - class' => static fn(string $n): string => NegateBC::negate($n),
		'BCMath - function' => static fn(string $n): string => negateBC($n),
		'String manipulation - class' => static fn(string $n): string => NegateString::negate($n),
		'String manipulation - function' => static fn(string $n): string => negateString($n),
		'Manual negation' => static function (string $n): string {
			if ($n === '0') {
				return '0';

			} elseif (strncmp($n, '-', 1) === 0) {
				return substr($n, 1);
			}

			return '-' . $n;
		},
	];

	$caseNo = 0;
	$steps = 1e6 - 1;

	foreach ($cases as $title => $callback) {
		echo '  ', ++$caseNo, '. ', $title, '...';

		$t = hrtime(true);

		$n = '123456789';
		for ($i = 0; $i < $steps; $i++) {
			$n = $callback($n);
		}

		assert($n === '-123456789');

		$t = hrtime(true) - $t;
		echo ' ', $t / 1e9, 's', "\n";
	}

	echo "\n";

})();


// =============================================================================
// === FRACTION - IS EQUAL TO ==================================================
// =============================================================================

(static function (): void {

	echo '> Fraction isEqualTo Benchmarks', "\n\n";

	$cases = [
		'Automatic canonicalization' => [
			static fn(string $n, string $d = '1'): FractionCanonicalized => new FractionCanonicalized($n, $d),
			static function (FractionCanonicalized $f): FractionCanonicalized { return $f; },
		],

		'Denominator multiplication' => [
			static fn(string $n, string $d = '1'): FractionIsEqualMultiplication => new FractionIsEqualMultiplication($n, $d),
			static function (FractionIsEqualMultiplication $f): FractionIsEqualMultiplication { return $f->canonicalize(); },
		],

		'Canonicalization inside isEqualTo()' => [
			static fn(string $n, string $d = '1'): FractionIsEqualCanonicalization => new FractionIsEqualCanonicalization($n, $d),
			static function (FractionIsEqualCanonicalization $f): FractionIsEqualCanonicalization { return $f->canonicalize(); },
		],
	];

	$caseNo = 0;
	$steps = 1e4;

	foreach ($cases as $title => $case) {
		echo '  ', ++$caseNo, '. ', $title, '...';

		$t = hrtime(true);
		[$factory, $canonicalization] = $case;

		$f = $factory('9223372036854775807');

		for ($i = 0; $i < $steps; $i++) {
			$f = $f->multiply($factory('9223372036854775806', '2'))
				->divide($factory('9223372036854775806', '2'))
				->add($factory('9223372036854775806', '2'))
				->subtract($factory('9223372036854775806', '2'));
		}

		$f = $f->subtract($factory('9223372036854775806'));

		assert($f->isEqualTo($factory('9223372036854775807', '9223372036854775807')));
		assert(!$f->isEqualTo($factory('9223372036854775808', '9223372036854775807')));

		assert($f->isEqualTo($factory('1')));
		assert(!$f->isEqualTo($factory('2')));

		assert($f->isEqualTo('1'));
		assert(!$f->isEqualTo('2'));

		$f = $canonicalization($f);
		assert($f->n === '1');
		assert($f->d === '1');

		$t = hrtime(true) - $t;
		echo ' ', $t / 1e9, 's', "\n";
	}

})();
