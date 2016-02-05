<?php

namespace WebChemistry\Routing;

use Nette;
use	Nette\Application\Routers\Route;
use Nette\Utils\Strings;

class Router implements IRouter {

	/**
	 * @param RouteManager $routeManager
	 */
	public function createRouter(RouteManager $routeManager) {
		$routeManager->addStyle('name');
		$routeManager->setStyleProperty('name', Route::FILTER_OUT, function($url) {
			return Strings::webalize($url);
		});
		$routeManager->setStyleProperty('name', Route::FILTER_IN, function($url) {
			return Strings::webalize($url);
		});

		// Admin
		$admin = $routeManager->getModule('Admin');
		$admin[] = new Route('admin[/<id [0-9]+>[-<name [0-9a-zA-Z\-]+>]]', [
			'presenter' => 'Homepage',
			'action' => 'default'
		]);
		$admin[] = new Route('admin/<presenter>[/<action>][/<id [0-9]+>[-<name [0-9a-zA-Z\-]+>]]', [
			'presenter' => 'Homepage',
			'action' => 'default'
		]);

		// Console
		$console = $routeManager->getModule('Console');
		$console[] = new Route('console[/<presenter>][/<action>][/<id [0-9a-zA-Z]+>]', [
			'presenter' => 'Homepage',
			'action' => 'default'
		]);

		// Front
		$front = $routeManager->getModule('Front');
		$front[] = new Route('<presenter>[/<action>][/<id [0-9]+>[-<name [0-9a-zA-Z\-]+>]]', [
			'presenter' => 'Homepage',
			'action' => 'default'
		]);

	}

}
