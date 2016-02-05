<?php

namespace App\UserModule\Components\Grids;

use WebChemistry\Grid\Grid;

class UserAdmin extends User {

	/**
	 * @return Grid
	 */
	protected function createComponentGrid() {
		$grid = parent::createComponentGrid();

		$grid->model = $grid->doctrineResource(
			$this->em->getRepository('Entity\User')->createQueryBuilder('a')
					 ->addSelect('r')
					 ->addSelect('a')
					 ->where('r.isAdmin = 1')
					 ->leftJoin('a.role', 'r'),
			[
				'role.name' => 'r.name',
				'role.isAdmin' => 'r.isAdmin'
			]
		);

	    return $grid;
	 }
}