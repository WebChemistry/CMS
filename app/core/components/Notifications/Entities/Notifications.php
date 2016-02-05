<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use WebChemistry\Exceptions\InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="Repository\Notifications")
 */
class Notifications extends Container {

	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer", length=9)
	 * @ORM\GeneratedValue
	 */
	public $id;

	/**
	 * @var \DateTime
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(type="datetime")
	 */
	public $created;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	public $message;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=30)
	 */
	protected $icon;

	/**
	 * @param string $icon
	 * @throws InvalidArgumentException
	 */
	public function setIcon($icon) {
		if (strlen($icon) > 30) {
			throw new InvalidArgumentException(sprintf('Max length of icon is 30, %d given.', strlen($icon)));
		}

		$this->icon = $icon;
	}

}
