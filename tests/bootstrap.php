<?php

namespace Simplex\Tests;

use Tester\Environment;


require_once __DIR__ . '/../Simplex/simplex.php';
require_once __DIR__ . '/../vendor/autoload.php';

Environment::setup();

function id($v) { return $v; }
