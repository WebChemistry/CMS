<?php

namespace App\UserModule\Components\Grids;

use Kdyby\Doctrine\EntityManager;
use WebChemistry\Components\Notifications;
use WebChemistry\Grid\Grid;
use WebChemistry\Grid\ControlContainer;
use WebChemistry\Grid\IFactory;

class User extends ControlContainer {

	/** @var \Nette\Security\User */
	private $user;

	public function __construct(EntityManager $em, Notifications $notifications, IFactory $factory,
								\Nette\Security\User $user) {
		parent::__construct($em, $notifications, $factory);
		$this->user = $user;
	}

	/**
	 * @return Grid
	 */
	protected function createComponentGrid() {
	    $grid = $this->createGrid();

		$grid->model = $grid->doctrineResource(
			$this->em->getRepository(\Entity\User::class)->createQueryBuilder('a')
					 ->addSelect('r')
					 ->addSelect('a')
					 ->leftJoin('a.role', 'r')
					 ->where('r.isAdmin = 0'),
			[
				'role.name' => 'r.name'
			]
		);

		$grid->addColumnText('name', 'user.admin.grid.name')
			 ->setSortable()
			 ->setFilterText()
			 ->setSuggestion();

		$grid->addColumnEmail('email', 'user.admin.grid.email')
			 ->setSortable()
			 ->setFilterText()
			 ->setSuggestion();

		$grid->addColumnText('role.name', 'user.admin.grid.roleName')
			 ->setSortable()
			 ->setFilterText()
			 ->setSuggestion();

		$grid->addColumnDate('registration', 'user.admin.grid.registration');

		$grid->addColumnDate('lastVisit', 'user.admin.grid.lastVisit')
			 ->setSortable();

		$grid->addActionControlHref('delete', 'grid.delete', 'delete!')
			 ->setIcon('trash-o')
			 ->setConfirm('grid.deleteConfirm');

		$grid->addActionHref('edit', 'grid.edit', 'User:edit')
			 ->setIcon('pencil');

		$grid->addActionHref('show', 'grid.show', 'User:show')
			 ->setIcon('eye');

		return $grid;
	}

	/**
	 * @param int $id
	 * @throws \Exception
	 */
	public function handleDelete($id) {
		$this->isAllowed('user:delete');

		if ($this->user->id == $id) {
			$this->flashMessage('user.admin.flashes.deleteYourself', 'error');
			$this->redraw();

			return;
		}

		$row = $this->em->getRepository(\Entity\User::class)->find($id);

		if (!$row) {
			$this->flashMessage('user.admin.flashes.notFound.', 'error');
			$this->redirect('this');
		}

		$this->createNotification('user.admin.notifications.delete', 'fa-male', $row->name);

		$this->em->remove($row);
		$this->em->flush();

		$this->flashMessage('user.admin.flashes.delete');
		$this->redraw();
	}

}
