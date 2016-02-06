<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/core/bootstrap.php';

$configurator = new WebChemistry\Configuration\Configuration;

$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/core/config/config.neon');
$configurator->addConfig(__DIR__ . '/core/config/console.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');
$configurator->addConfig(__DIR__ . '/config/parameters.neon');
$configurator->addConfig(__DIR__ . '/config/development.neon');
//$configurator->addConfig(__DIR__ . '/config/production.neon');
$configurator->addAutoloadConfig(__DIR__ . '/modules/', 'config.neon', 1);
$configurator->addAutoloadConfig(__DIR__ . '/other', 'config.neon', 1);

return $configurator->createContainer();
