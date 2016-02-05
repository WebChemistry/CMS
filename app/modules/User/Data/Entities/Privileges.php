<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Privileges {

    /**
	 * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", length=9)
     * @ORM\GeneratedValue
     */
    public $id;

	/**
	 * @var array
	 * @ORM\Column(type="json_array")
	 */
	public $allow = [];

	/**
	 * @var Role
	 * @ORM\OneToOne(targetEntity="Entity\Role", mappedBy="privileges")
	 */
	public $role;

}
