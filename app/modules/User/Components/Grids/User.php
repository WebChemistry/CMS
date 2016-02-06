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

	/** @var string For UserAdmin */
	protected $userSelect = 'partial a.{id, name, email, registration}, partial r.{id, name}';

	/**
	 * @param EntityManager $em
	 * @param Notifications $notifications
	 * @param IFactory $factory
	 * @param \Nette\Security\User $user
	 */
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
		$grid->model = $grid->doctrineResource($this->em->getRepository(\Entity\User::class)
			->createQueryBuilder('a')
			->select($this->userSelect)
			->where('a.role IS NULL OR r.isAdmin = 0')
			->leftJoin('a.role', 'r'), [
			'role.name' => 'r.name',
		]);

		$grid->addColumnText('name', 'user.admin.grid.name')
			 ->setSortable()
			 ->setFilterText()
			 ->setSuggestion();

		$grid->addColumnEmail('email', 'user.admin.grid.email')
			 ->setSortable()
			 ->setFilterText()
			 ->setSuggestion();

		$grid->addColumnText('role.name', 'user.admin.grid.roleName')
			->setCustomRender(function ($entity) {
				if ($entity->role) {
					return $entity->role->name;
				}

				return NULL;
			})
			->setSortable()
			->setFilterText()
			->setSuggestion();

		$grid->addColumnDate('registration', 'user.admin.grid.registration');

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
