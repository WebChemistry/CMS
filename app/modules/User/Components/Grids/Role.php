<?php

namespace App\UserModule\Components\Grids;

use WebChemistry\Grid\Grid;
use WebChemistry\Grid\BaseControl;


class Role extends BaseControl {

	/**
	 * @return Grid
	 */
	protected function createComponentGrid() {
	    $grid = $this->createGrid();

		$grid->model = $grid->doctrineResource(
			$this->em->getRepository(\Entity\Role::class)->createQueryBuilder('a')
		);

		$grid->addColumnText('name', 'role.admin.grid.name')
			 ->setSortable()
			 ->setFilterText();

		$grid->addActionControlHref('delete', 'grid.delete', 'deleteRole!')
			 ->setIcon('trash-o')
			 ->setConfirm('grid.deleteConfirm');

		$grid->addActionHref('editRole', 'grid.edit', 'User:editRole')
			 ->setIcon('pencil');

		return $grid;
	}

	public function handleDeleteRole($id) {
		$row = $this->getRoleRepository()->find($id);
		if (!$row) {
			$this->flashMessage('role.admin.flashes.roleNotFound', 'error');

			$this->redraw();
			return;
		}
		if ($this->getRoleRepository()->hasUsers($id)) {
			$this->flashMessage('role.admin.flashes.roleHasUsers', 'error');

			$this->redraw();
			return;
		}

		$this->em->remove($row);
		$this->em->flush();

		$this->createNotification('role.admin.notification.delete', 'fa-users', $row->name);

		$this->flashMessage('role.admin.flashes.roleDeleted');
		$this->redraw();
	}

	/**
	 * @return \Repository\Role
	 */
	private function getRoleRepository() {
	    return $this->em->getRepository(\Entity\Role::class);
	}

}
