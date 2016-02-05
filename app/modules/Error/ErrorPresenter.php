<?php

namespace App\Presenters;

use Nette\Application\BadRequestException;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Tracy\Debugger;
use Tracy\ILogger;
use WebChemistry\Error;

class ErrorPresenter extends BasePresenter {

	/** @var IRouter */
	public $router;

	/** @var ILogger */
	public $logger;

	/**
	 * @param IRouter $router
	 */
	public function __construct(IRouter $router) {
		$this->router = $router;
	}

	public function actionDefault(\Exception $exception) {
		$this->logger = Debugger::getLogger();

		if ($exception instanceof BadRequestException) {
			//$code = $exception->getCode();
			//$this->logger->log("HTTP code $code: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}", 'access');
		} else {
			$this->logger->log($exception, ILogger::EXCEPTION);
		}
		if ($this->isAjax()) {
			$this->payload->error = TRUE;
			$this->terminate();
		}

		$matches = $this->router->match($this->getHttpRequest());

		if ($matches instanceof Request) {
			$explode = explode(':', $matches->presenterName);
			if (count($explode) === 1) {
				$module = NULL;
			} else {
				$module = ':' . $explode[0];
			}

			$this->forward("$module:Error:", [
				'exception' => $exception
			]);
		} else {
			Error::handleErrorPage($exception);
			$this->terminate();
		}
	}

}
