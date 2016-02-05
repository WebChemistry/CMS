<?php

namespace App\UserModule;

use Entity\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Object;
use WebChemistry\Administration\Helper;
use WebChemistry\Administration\IBox;
use WebChemistry\Administration\ILabel;

class Settings extends Object implements IBox {

	const ICON = 'fa-users';

	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/** @var \WebChemistry\Administration\Helper */
	private $helper;

	/**
	 * @param EntityManager $em
	 * @param Helper $helper
	 */
	public function __construct(EntityManager $em, Helper $helper) {
		$this->em = $em;
		$this->helper = $helper;
	}

	/**
	 * @return string
	 */
	public function getBox() {
		return $this->helper->createBox('user.box.users', $this->em->getRepository(User::class)->countBy(), self::ICON);
	}

}
