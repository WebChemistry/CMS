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

}
