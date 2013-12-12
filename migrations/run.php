<?php

use Migration\Extensions\OrmPhp;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/Configurator.php';

set_time_limit(0);
ini_set('memory_limit', '1G');

$configurator = new App\Configurator;
$configurator->enableDebugger();
$configurator->createRobotLoader()->register();

$context = $configurator->createContainer();

$console = new Migration\Console\Application($configurator, $context);
$console->setDirectory(__DIR__);
if (php_sapi_name() === "cli")
{
	$console->run();
}
else
{
	$console->runWithArgs(array(
        '--reset' => isset($_GET['reset']),
        '--data' => isset($_GET['data']),
    ));
}
