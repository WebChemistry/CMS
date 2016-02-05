<?php

namespace Entity;

use Nette\Object;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Parameter extends Object {

	/** @var bool */
	public static $strict = TRUE;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=50)
	 */
	protected $id;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $content;

	/**
	 * @ORM\Column(type="boolean", options={"default"="0"})
	 */
	protected $isSerialized = FALSE;

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	public function setContent($content) {
		if ($this->isSerialized) {
			if (!is_array($content)) {
				if (self::$strict === TRUE) {
					throw new \Exception(sprintf('Parameters %s must be array, %s given.', $this->id, gettype($this->content)));
				} else {
					return NULL;
				}
			} else {
				$this->content = serialize($content);
			}
		} else {
			$this->content = $content;
		}

		return $this;
	}

	public function getContent() {
		if ($this->isSerialized) {
			return unserialize($this->content);
		}

		return $this->content;
	}

	public function setIsSerialized($serialized) {
		$this->isSerialized = $serialized;

		return $this;
	}

	public function getIsSerialized() {
		return $this->isSerialized;
	}

	public function isSerialized() {
		return $this->isSerialized;
	}

}
