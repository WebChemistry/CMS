<?php

namespace Repository;

use Entity;

/**
 * @method Entity\Role find($id)
 */
class Role extends Container {

	/**
	 * Returns [][id => name]
	 * @return array
	 */
	public function getPairs() {
		return $this->findPairs([], 'name', 'id');
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function hasUsers($id) {
		return (bool) $this->createQueryBuilder('e')
			->where('e.id = :id')
			->setParameter('id', $id)
			->select('COUNT(e)')
			->setMaxResults(1)
			->getQuery()->getSingleScalarResult();
	}

}
