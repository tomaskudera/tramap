<?php

$STOP_AFTER_REQUIRE_LOADED = true;

//define CFG_DIR
define('CFG_DIR', dirname(__DIR__). '/config');
//constants web related constants
define('ROOT', __DIR__);
define('THEMES', dirname(__DIR__). '/themes');
define('TMP', dirname(__DIR__). '/tmp');
//there is the real application stored, can vary
define('SYSTEM', dirname(dirname(__DIR__)).'/cms');
//put application itself into control
require SYSTEM . '/App/bootstrap.php';
include APP . '/worker.php';
