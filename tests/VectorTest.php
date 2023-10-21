<?php

declare(strict_types = 1);

namespace Simplex\Tests;

use Tester\Assert;
use Tester\TestCase;
use Simplex\Math\Vector;
use Simplex\Math\Fraction;
use Simplex\EmptyVectorException;


require_once __DIR__ . '/bootstrap.php';


final class VectorTest extends TestCase
{

	/**
	 * @param  array<int|float|string|Fraction> $values
	 * @param  Fraction[] $expectedValues
	 * @dataProvider provideCreationData
	 */
	public function testCreation(array $values, array $expectedValues, int $expectedCount): void
	{
		$vectorConstructor = new Vector($values);
		Assert::equal($expectedValues, $vectorConstructor->toArray());
		Assert::count($expectedCount, $vectorConstructor);

		$vectorFactory = Vector::create($values);
		Assert::equal($expectedValues, $vectorFactory->toArray());
		Assert::count($expectedCount, $vectorFactory);
	}


	/** @return array<int, array{array<int|float|string|Fraction>, Fraction[], int}> */
	public function provideCreationData(): array
	{
		return [
			[
				[1, '2', 3.0, new Fraction('4')],
				[new Fraction('1'), new Fraction('2'), new Fraction('3'), new Fraction('4')],
				4,
			],
			[
				['a' => 1, 'b' => '2', 3.0, 'xyz' => new Fraction('4')],
				[new Fraction('1'), new Fraction('2'), new Fraction('3'), new Fraction('4')],
				4,
			],
		];
	}


	public function testEmptyVector(): void
	{
		Assert::exception(static function (): void {
			new Vector([]);

		}, EmptyVectorException::class, 'Vector must have at least one value.');

		Assert::exception(static function (): void {
			Vector::create([]);

		}, EmptyVectorException::class, 'Vector must have at least one value.');
	}

}


(new VectorTest)->run();
