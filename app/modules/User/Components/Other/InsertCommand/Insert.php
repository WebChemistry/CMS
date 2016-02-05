<?php

namespace App\UserModule\Components\InsertCommand;

use Entity\Role;
use Entity\User;
use Kdyby\Doctrine\EntityManager;
use WebChemistry\Console\IInsert;

class Insert implements IInsert {

	/** @var EntityManager */
	private $em;

	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	public function execute() {
		if (!$this->em->getRepository(Role::class)->countBy()) {
			$role = new Role();
			$role->name = 'Owner';
			$role->allowAll = TRUE;
			$role->monitor = FALSE;
			$role->isAdmin = TRUE;

			$this->em->persist($role);
			$this->em->flush();
		}

		if (!$this->em->getRepository(User::class)->countBy()) {
			$user = new User();
			$user->name = 'Administrator';
			$user->setPassword('admin');
			$user->email = 'admin@example.com';
			$user->role = $this->em->getRepository(Role::class)->find(1);

			$this->em->persist($user);
			$this->em->flush();
		}
	}

}
