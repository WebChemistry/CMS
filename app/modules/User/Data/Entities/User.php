<?php

namespace Entity;

use Doctrine;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Nette\Security\Passwords;
use WebChemistry\User\Interfaces\IUser;
use WebChemistry\User\Interfaces\IRole;

/**
 * @ORM\Entity(repositoryClass="Repository\User")
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 *
 * @property-read int $id
 * @property string $password
 */
class User extends Container implements IUser {

	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer", length=9)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=100)
	 */
	public $name;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true, length=80)
	 */
	public $email;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255)
	 */
	protected $password;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	public $avatar;

	/**
	 * @var Role
	 * @ORM\ManyToOne(targetEntity="Role")
	 */
	public $role;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 * @Gedmo\Timestampable(on="create")
	 */
	public $registration;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=10, nullable=true)
	 */
	public $forgetHash;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	public $forgetTime;

	/**
	 * @var \DateTime
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(type="datetime")
	 */
	public $lastVisit;

	/**
	 * @ORM\PrePersist
	 * @param Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function beforeInsert(Doctrine\ORM\Event\LifecycleEventArgs $args) {
		if (!$this->role && $this->useRole()) {
			$this->role = $args->getEntityManager()
							   ->getRepository(Role::class)
							   ->getMember();
		}
	}

	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $password
	 * @return self
	 */
	public function setPassword($password) {
		if ($password) {
			$this->password = Passwords::hash($password);
		}

		return $this;
	}

	/**
	 * @ORM\PreRemove
	 * @param Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function beforeRemove(Doctrine\ORM\Event\LifecycleEventArgs $args) {

	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @return IRole|Role
	 */
	public function getRole() {
		return $this->role;
	}

	/**
	 * @return string
	 */
	public function getUserName() {
		return $this->name;
	}

	/**
	 * @return bool
	 */
	public function useRole() {
		return TRUE;
	}

	/**
	 * @return bool
	 */
	public function isMonitoring() {
		return $this->getRole()->monitor;
	}

	/**
	 * @return string
	 */
	public function getAvatar() {
		return $this->avatar;
	}

}
