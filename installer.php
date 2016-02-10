<?php
if (file_exists(__DIR__ . '/app/bootstrap.php') && in_array($_GET['type'], Installer::$needContainer)) {
	Installer::$container = require __DIR__ . '/app/bootstrap.php';
}
?>
	<meta name="robots" content="noindex, nofollow">
<?php
//exit('Change me if you want any action.'); // Delete this
?>
	<a href="?type=extract">Extract project (extract all from project.zip)</a><br>
	<a href="?type=truncate">Truncate project (removes all from current directory)</a><br>
	<a href="?type=deleteZip">Delete zip (removes project.zip)</a><br>
	<a href="?type=createTables">Create tables</a><br>
	<a href="?type=insertValues">Insert values to db</a><br>
	<a href="?type=createHtaccess">Create .htaccess for url without /www/</a><br>
	<a href="?type=initMode">Init mode (truncate cache, assets, removes composer.json, .gitignore,node_modules,...)</a><br>
	<a href="?type=deleteSelf">Delete this file</a>

	<br><br><strong style="font-size:18px">
<?php

if (!$_GET['type']) {
	exit();
}

class Installer {

	/** @var array */
	public static $needContainer = ['initMode', 'createTables', 'insertValues'];

	/** @var \Nette\DI\Container */
	public static $container;

	/**
	 * @return \Nette\DI\Container
	 */
	private function requireBootstrap() {
		if (!file_exists(__DIR__ . '/app/bootstrap.php')) {
			exit('./app/bootstrap.php not exists.');
		}

		return self::$container;
	}

	public function createTables() {
		$container = $this->requireBootstrap();
		/** @var \Kdyby\Doctrine\EntityManager $em */
		$em = $container->getByType('Kdyby\Doctrine\EntityManager');
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
		try {
			$schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());
		} catch (\Doctrine\ORM\Tools\ToolsException $e) {
			exit($e->getMessage());
		}
	}

	public function insertValues() {
		$container = $this->requireBootstrap();
		/** @var \App\Console\Insert $insert */
		$insert = $container->getByType('App\Console\Insert');
		$insert->apply();
	}

	public function initMode() {
		$this->requireBootstrap();
		foreach (['.gitignore', 'composer.json', 'composer.lock', 'Gruntfile.js', 'package.json'] as $file) {
			@unlink(__DIR__ . '/' . $file);
		}
		// Temp
		$this->truncate(__DIR__ . '/temp/cache', FALSE);
		$this->truncate(__DIR__ . '/temp/cronner', FALSE);
		$this->truncate(__DIR__ . '/temp/critical-section', FALSE);
		$this->truncate(__DIR__ . '/temp/proxies', FALSE);
		@unlink(__DIR__ . '/temp/btfj.dat');
		if (file_exists(__DIR__ . '/temp')) {
			foreach (\Nette\Utils\Finder::findFiles('*.php*')->in(__DIR__ . '/temp') as $file) {
				unlink((string) $file);
			}
		}

		// log
		foreach (\Nette\Utils\Finder::findFiles('*.html, *.txt')->in(__DIR__ . '/log') as $file) {
			unlink((string) $file);
		}

		// node_modules
		$this->truncate(__DIR__ . '/node_modules', TRUE);

		// phpstorm
		$this->truncate(__DIR__ . '/.idea', TRUE);

		// Assets
		foreach (\Nette\Utils\Finder::findDirectories('*')->in(__DIR__ . '/www/assets')->exclude('default') as $dir) {
			$this->truncate((string) $dir, TRUE);
		}
		// Assets default without original
		foreach (\Nette\Utils\Finder::findDirectories('*')->in(__DIR__ . '/www/assets/default')->exclude('original') as $dir) {
			$this->truncate((string) $dir, TRUE);
		}
	}

	public function deleteZip($file) {
		@unlink($file);
	}

	public function extract($file, $extractTo) {
		try {
			$zip = new ZipArchive();
			$zip->open($file);
			$zip->extractTo($extractTo);
			$zip->close();
		} catch (\Exception $e) {
			exit('Cannot extract file reason is: ' . $e->getMessage());
		}
	}

	public function truncate($dir, $removeCurrentDir = FALSE) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") {
						$this->truncate($dir . "/" . $object, TRUE);
					} else {
						unlink   ($dir."/".$object);
					}
				}
			}
			reset($objects);
			if ($removeCurrentDir) {
				rmdir($dir);
			}
		}
	}

	public function deleteSelf() {
		unlink(__FILE__);
	}

	public function createHtaccess() {
		$path = str_replace(basename(__FILE__), '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$string = "<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^$ " . $path . "www/ [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} !^" . $path . "www/
    RewriteRule ^(.*)$ " . $path . "www/$1
</IfModule>";

		if (!file_exists(__DIR__ . '/.htaccess')) {
			file_put_contents(__DIR__ . '/.htaccess', $string);
		}
	}

}

$installer = new Installer();
switch ($_GET['type']) {
	case 'truncate':
		$installer->truncate(__DIR__ . '/');
		break;
	case 'extract':
		$installer->extract(__DIR__ . '/project.zip', __DIR__ . '/');
		break;
	case 'deleteZip':
		$installer->deleteZip(__DIR__ . '/project.zip');
		break;
	case 'createTables':
		$installer->createTables();
		break;
	case 'insertValues':
		$installer->insertValues();
		break;
	case 'initMode':
		$installer->initMode();
		break;
	case 'deleteSelf':
		$installer->deleteSelf();
		exit('I died');
		break;
	case 'createHtaccess':
		$installer->createHtaccess();
		break;
	default:
		exit('Command not exists.');
}

exit('Command was successful');
