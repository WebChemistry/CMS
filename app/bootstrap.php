<?php

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/installed/core/webchemistry-cms/core/bootstrap.php')) {
	require __DIR__ . '/installed/core/webchemistry-cms/core/bootstrap.php';
}

$configurator = new WebChemistry\Configuration\Configuration;

$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/other')
	->addDirectory(__DIR__ . '/modules')
	->register();

if (file_exists($configFile = __DIR__ . '/config/composer-configs.json')) {
	$configs = json_decode(file_get_contents($configFile), TRUE);
	foreach ($configs as $config) {
		$configurator->addConfig(__DIR__ . '/../' . $config);
	}
}

$configurator->addConfig(__DIR__ . '/config/config.local.neon');
$configurator->addConfig(__DIR__ . '/config/parameters.neon');
$configurator->addConfig(__DIR__ . '/config/development.neon');
//$configurator->addConfig(__DIR__ . '/config/production.neon');
$configurator->addAutoloadConfig(__DIR__ . '/modules/', 'config.neon', 1);
$configurator->addAutoloadConfig(__DIR__ . '/other', 'config.neon', 1);

return $configurator->createContainer();
