<?php

namespace App\Presenters\FrontModule;

use WebChemistry\Error;

class ErrorPresenter extends FrontPresenter {

	public function renderDefault(\Exception $exception) {
		Error::handleErrorPage($exception);

		$this->terminate();
	}

}
