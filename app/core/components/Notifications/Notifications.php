<?php

namespace WebChemistry\Components;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Translation\Translator;
use Nette\Application\UI\ITemplate;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Object;
use Nette\Security\User;
use WebChemistry\Utils\DateTime;

class Notifications extends Object {

	const COOKIE_KEY = 'notifications-admin';

	/** @var EntityManager */
	private $entityManager;

	/** @var User */
	private $user;

	/** @var Request */
	private $request;

	/** @var \Nette\Http\Response */
	private $response;

	/** @var Translator */
	private $translator;

	/**
	 * @param EntityManager $entityManager
	 * @param User $user
	 * @param Request $request
	 * @param Response $response
	 */
	public function __construct(EntityManager $entityManager, User $user, Request $request, Response $response,
								Translator $translator = NULL) {
		$this->entityManager = $entityManager;
		$this->user = $user;
		$this->request = $request;
		$this->response = $response;
		$this->translator = $translator;
	}

	/**
	 * @param string $message %user == user_name
	 * @param string $icon
	 */
	public function createNotification($message, $icon) {
		if ($this->user->isMonitoring()) {
			if ($this->translator) {
				$message = $this->translator->translate($message);
			}
			$args = array_slice(func_get_args(), 2);
			array_unshift($args, $message);
			$args[0] = str_replace(['%s', '%user'], ['<b>%s</b>', '<b>' . $this->user->getName() . '</b>'], $args[0]);

			$this->entityManager->getRepository(\Entity\Notifications::class)->insert(call_user_func_array('sprintf', $args),
				$icon);
		}
	}

	/**
	 * @param ITemplate $template
	 */
	public function notificationToTemplate(ITemplate $template) {
		$cookie = $this->request->getCookie(self::COOKIE_KEY);

		/** @var \Repository\Notifications $repository */
		$repository = $this->entityManager->getRepository('Entity\Notifications');

		$template->notifications = $repository->getLast();
		$template->notification_count = $repository->getCountFrom(DateTime::from($cookie));
	}

}
