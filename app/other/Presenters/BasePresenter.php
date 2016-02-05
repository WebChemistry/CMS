<?php

namespace App\Presenters;

use Repository\User;
use WebChemistry\Presenters;

abstract class BasePresenter extends Presenters\BasePresenter {

	protected function beforeRender() {
		parent::beforeRender();

		if (!$this->isAjax() && $this->user->isLoggedIn()) {
			/** @var User $repository */
			$repository = $this->em->getRepository(\Entity\User::class);
			$repository->updateLastVisit($this->user->id);
		}
	}

}
