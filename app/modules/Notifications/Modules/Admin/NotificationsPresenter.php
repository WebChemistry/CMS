<?php

namespace App\Presenters\AdminModule;

use Entity\Notifications;

/**
 * @isAllowed notifications
 */
class NotificationsPresenter extends AdminPresenter {

	/** @persistent */
	public $filter;

	public function renderDefault() {
		$this->template->notifications = $this->getNotificationsRepository()->getLast(70, $this->filter);
		$this->template->icons = $this->getNotificationsRepository()->getDistinctIcons();
	}

	/**
	 * @return \Repository\Notifications
	 */
	private function getNotificationsRepository() {
	    return $this->em->getRepository(Notifications::class);
	}

}
