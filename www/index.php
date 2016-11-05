<?php

//define CFG_DIR
define('CFG_DIR', dirname(__DIR__). '/config');
//constants web related constants
define('ROOT', __DIR__);
define('THEMES', dirname(__DIR__). '/themes');
define('TMP', dirname(__DIR__). '/tmp');
//there is the real application stored, can vary
define('SYSTEM', dirname(dirname(__DIR__)).'/tramap');
define('APP', SYSTEM . '/App');
define('VENDOR', SYSTEM . '/vendor');

define('ADMIN_APP', strpos($_SERVER['HTTP_HOST'], 'admin') !== FALSE);

//put application itself into control
require SYSTEM . '/App/bootstrap.php';
