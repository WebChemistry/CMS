<?php

namespace Repository;

use Entity;
use Nette\Utils\DateTime;

/**
* @name notifications
*/
class Notifications extends Container {

	/**
	 * @param int $limit
	 * @param string $icon
	 * @return array
	 */
	public function getLast($limit = 15, $icon = NULL) {
		$criteria = [];
		if ($icon !== NULL) {
			$criteria['icon'] = $icon;
		}

		return $this->findBy($criteria, ['id' => 'DESC'], $limit);
	}

	/**
	 * @param string $strTime
	 */
	public function clean($strTime) {
		if ($strTime !== NULL) {
			$this->createQueryBuilder('e')
				 ->where('e.created < :dateTime')
				 ->setParameter('dateTime', DateTime::from($strTime))
				 ->delete()
				 ->getQuery()
				 ->getResult();
		}
	}

	/**
	 * @param string $message
	 * @param string $icon
	 */
	public function insert($message, $icon) {
		$entity = new $this->_entityName;

		$entity->message = $message;
		$entity->icon = $icon;

		$this->_em->persist($entity);
		$this->_em->flush();
	}

	/**
	 * @param \DateTime $dateTime
	 * @return int
	 */
	public function getCountFrom(\DateTime $dateTime) {
		return $this->countBy(['created >' => $dateTime]);
	}

	/**
	 * @return array
	 */
	public function getDistinctIcons() {
		return $this->createQueryBuilder('a')->select('DISTINCT a.icon')->getQuery()->getResult();
	}

}
