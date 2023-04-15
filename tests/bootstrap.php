<?php

namespace Simplex\Tests;

use Tester\TestCase;
use Tester\Environment;


require_once __DIR__ . '/../Simplex/simplex.php';
require_once __DIR__ . '/../vendor/autoload.php';

Environment::setup();

/** @return TestCase */
function id(TestCase $v) { return $v; }
