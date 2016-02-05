<?php

namespace Repository;

use Entity;

/**
 * @method Entity\Role find($id)
 */
class Role extends Container {

	const MEMBER = 2;

	/**
	 * @return Entity\Role
	 */
	public function getMember() {
		return $this->find(self::MEMBER);
	}

}
