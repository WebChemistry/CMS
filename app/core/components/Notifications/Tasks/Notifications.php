<?php

namespace Task;

use Kdyby\Doctrine\EntityManager;

class Notifications {

	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @cronner-task Clean old notifications
	 * @cronner-period 1 week
	 * @cronner-days working days
	 * @cronner-time 01:00 - 03:00
	 */
	public function cleanNotifications() {
		/** @var \Repository\Notifications $repository */
		$repository = $this->em->getRepository(\Entity\Notifications::class);
		$repository->clean('- 30 days');
	}

}
