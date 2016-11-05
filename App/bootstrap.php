<?php

// block: constants definitions
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

//$definitions_set = true;
// block end

require __DIR__ . '/../vendor/autoload.php';			// composer autoloader
$loader = new \Phalcon\Loader();
$loader->registerNamespaces(
	[
		'Core' => '../App/Core/',
		'Main' => '../App/Main/',
		'Admin' => '../App/Admin/',
		'Router' => '../App/Router/',
		'I18n' => '../Lib/I18n/',
	]
);
$loader->register();
//require dirname(__DIR__) . '/core/autoload.php';		// my autoloader


use Netpromotion\Profiler\Profiler;

define('WEB_ROOT', dirname(__DIR__).'/www');
Profiler::enable();
Profiler::start();

$configurator = new Nette\Configurator;

//$configurator->setDebugMode(false); // enable for your remote IP
$configurator->setDebugMode(['83.240.102.230']); // enable for your remote IP
$logDir = dirname(__DIR__) . '/tmp/log';
if (!is_dir($logDir)) {
	mkdir($logDir);
}
//$configurator->setDebugMode(FALSE);
$configurator->enableDebugger($logDir);


$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(dirname(__DIR__) . '/tmp');

//$configurator->createRobotLoader()
//	->addDirectory(__DIR__)
//	->register();

$configSuffix = '';
if (ADMIN_APP) {
	$configSuffix = '.admin';
}
$configurator->addConfig(dirname(__DIR__) . "/config/config$configSuffix.neon");
$configurator->addConfig(dirname(__DIR__) . '/config/config.local.neon');


Profiler::start('container');
$container = $configurator->createContainer();

$router = new Router\MyEasyRouter($container->getService('translator'));

$container->addService('router',$router->create());

Profiler::finish('container');
// NOTE return statement is commented, because in index.php there is no application run call
//return $container;

// NOTE devel version, remove when fixed
$container->getService('application')->run();

Netpromotion\Profiler\Profiler::finish();
