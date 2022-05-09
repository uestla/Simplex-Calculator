Simplex Calculator
==================

Live demo: http://simplex.tode.cz

This is a simple PHP tool for linear programming problems solving using dual simplex algorithm.

It can detect none, one and only and more optimal solutions to any linear problem you tell it to solve.

However, the only thing that has been left unimplemented is basis cycling detection. Feel free to post pull requests ;-)

Enjoy.


Conventions
-----------

Please name your variables as **x&lt;n&gt;** where *&lt;n&gt;* is a natural number (
see [example.php](https://github.com/uestla/Simplex-Calculator/blob/master/example.php) for details).


Using LinearProgram class
-------------------------

Using `\Simplex\Formulation\LinearProgram` you can formulate linear program solution.

For example let's assume, that we have given linear problem:

> z = 2x1 + x2

with constraints:
> x1 + x2 >= 2
>
> x1 - x2 = 0
>
> x1 - x2 >= -4

that we can describe in following way:

```php
use \Simplex\Formulation\LinearProgram;
use \Simplex\Formulation\Formula;
use \Simplex\Formulation\GreaterOrEqual;
use \Simplex\Formulation\Equal;

$program = new LinearProgram(
    new Formula(2, 1),
    [
        new GreaterOrEqual(new Formula(1, 1), 2), 
        new Equal(new Formula(1, -1), 0), 
        new GreaterOrEqual(new Formula(1, -2), -4),
    ]
);
```

Now we can take maximum solution with only:

```php
$program->getMax(); // return 12
```

And optimized parameters:

```php
$solution = $program->getSolutionFormula();
// returns new Formula(4, 4, 6, 0);

$solution->getParam('x1'); // 4
$solution->getParam(1); // the same as upper 4
```

see [example_simplified.php](https://github.com/uestla/Simplex-Calculator/blob/master/example_simplified.php) for
details.
