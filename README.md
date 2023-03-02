# Simplex Calculator

## About

PHP library for solving linear programming problems using dual simplex algorithm.

**NOTE:** Basis cycling detection is not implemented.


## Installation

The recommended way is to use [Composer](https://getcomposer.org/):

```bash
composer require uestla/simplex-calculator
```

You can also download the latest [release](https://github.com/uestla/Simplex-Calculator/tags) as a ZIP file.


## Conventions

Please name all your input variables as `x<n>` where `<n>` is a natural number (see [example below](#defining-and-solving-simplex-tasks) for details).


## Usage

### Loading library

```php
// using Composer
require_once __DIR__ . '/vendor/autoload.php';

// using manual download
require_once __DIR__ . '/Simplex/simplex.php';
```

### Defining and solving simplex tasks

```php
// define task: Maximize x1 + 2x2
$task = new Simplex\Task(new Simplex\Func(array(
	'x1' => 1,
	'x2' => 2,
)));

// add constraints

// 3x1 + 2x2 <= 24
$task->addRestriction(new Simplex\Restriction(array(
	'x1' => 3,
	'x2' => 2,

), Simplex\Restriction::TYPE_LOE, 24));

// -2x1 - 4x2 >= -32
$task->addRestriction(new Simplex\Restriction(array(
	'x1' => -2,
	'x2' => -4,

), Simplex\Restriction::TYPE_GOE, -32));

// create solver
$solver = new Simplex\Solver($task);

// get solutions
$solution = $solver->getSolution(); // array('x1' => 0, 'x2' => 8, 'x3' => 8, 'x4' => 0)
$alternativeSolutions = $solver->getAlternativeSolution(); // array(array('x1' => 4, 'x2' => 6, 'x3' => 0, 'x4' => 0))

// get optimal value
$optimum = $solver->getSolutionValue($solution); // 16

// print solutions
$printer = new Simplex\Printer;
$printer->printSolution($solver);

// or print the whole solution process
$printer->printSolver($solver);
```


### Solutions

`$solver->getSolutions()` returns primary optimal solution. The value can be of 3 types:

- `array` - vector with optimal coefficients to each input variable
- `false` - optimal solution does not exist
- `null` - not enough steps to find solution

By default, solver stops the calculation after 16 simplex tables. You can increase the maximum steps limit with the second parameter:

```php
$solver = new Simplex\Solver($task, 32);
```

`$solver->getAlternativeSolutions()` returns array of other optimal solutions if any have been found. It may return:

- `array` - array of vectors with alternative optimal solutions
- `null` - no alternative optimal solution found
