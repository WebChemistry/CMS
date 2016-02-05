<?php

namespace App\Presenters\AdminModule;

use WebChemistry\Error;

class ErrorPresenter extends AdminPresenter {

	public function renderDefault(\Exception $exception) {
		$this->template->code = $code = Error::getCode($exception);
		$this->template->message = isset(Error::$messages[$code]) ? Error::$messages[$code] : Error::$messages[500];
	}

}
