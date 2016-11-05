<?php

define('CFG_DIR', dirname(__DIR__). '/config');
define('TMP', dirname(__DIR__). '/tmp');
define('THEMES', dirname(__DIR__). '/themes');
define('ROOT', __DIR__. '/');

$STOP_AFTER_REQUIRE_LOADED = true;

//put application itself into control
require '../../cms/app/bootstrap.php';

$request = new \core\routing\Request();

dd(\core\ui\Template::css());
