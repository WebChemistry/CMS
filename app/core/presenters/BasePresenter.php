<?php

namespace WebChemistry\Presenters;

use WebChemistry\Application\Presenter;

abstract class BasePresenter extends Presenter {

	protected function beforeRender() {
		parent::beforeRender();

		if ($this->isAjax()) {
			$this->redrawControl('flashes');
		}
	}

}
