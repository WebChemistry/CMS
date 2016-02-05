<?php

namespace App\Routers;

use	Nette\Application\Routers\Route;
use Nextras\Routing\StaticRouter;
use WebChemistry\Routing\IRouter;
use WebChemistry\Routing\RouteManager;
use WebChemistry\Routing\Router;

class LocalRouter extends Router implements IRouter {

	/**
	 * @param RouteManager $routeManager
	 * @return void
	 */
	public function createRouter(RouteManager $routeManager) {
		parent::createRouter($routeManager);
	}

}
