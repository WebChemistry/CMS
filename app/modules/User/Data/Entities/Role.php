<?php

namespace Entity;

use Doctrine;
use Doctrine\ORM\Mapping as ORM;
use WebChemistry\User\Interfaces\IRole;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Role implements IRole {

    /**
	 * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", length=9)
     * @ORM\GeneratedValue
     */
    public $id;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=120)
	 */
	public $name;

	/**
	 * @var Privileges[]
	 * @ORM\ManyToOne(targetEntity="Entity\Privileges", cascade={"persist", "merge", "remove"})
	 */
	public $privileges;

	/**
	 * @var Role
	 * @ORM\ManyToOne(targetEntity="Entity\Role")
	 */
	public $extends;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	public $allowAll = FALSE;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	public $isAdmin = FALSE;
	
	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	public $monitor = FALSE;

	/**
	 * @var User[]
	 * @ORM\OneToMany(targetEntity="Entity\User", mappedBy="role")
	 */
	public $users;

	public function __construct() {
		$this->users = new Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @ORM\PrePersist()
	 */
	public function beforeInsert() {
		$this->privileges = new Privileges();
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return bool
	 */
	public function isAdmin() {
		return $this->isAdmin;
	}

	/**
	 * @return bool
	 */
	public function isSuperAdmin() {
		return $this->allowAll;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

}
