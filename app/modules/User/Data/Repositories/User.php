<?php
namespace Repository;

use Doctrine\ORM\NoResultException;
use Entity;
use WebChemistry\User\Interfaces\IRepository;

/**
 * @method Entity\User find($id)
 */
class User extends Container implements IRepository {

	const COLUMN = 'email';

	/**
	 * @param string $query
	 * @param int $limit
	 * @return array
	 */
	public function suggestUser($query, $limit = 10) {
		$return = [];
		$result = $this->createQueryBuilder('e')
			->where('e.name LIKE :search')
			->orWhere('e.email LIKE :search')
			->setParameter('search', '%' . $query . '%')
			->setMaxResults($limit)
			->select('e.name, e.email')
			->getQuery()
			->getResult();
		foreach ($result as $row) {
			$return[$row['email']] = $row['name'] . ' <' . $row['email'] . '>';
		}

		return $return;
	}

	/**
	 * @param string $email
	 * @return bool
	 */
	public function validateUser($email) {
		return (bool) $this->countBy([self::COLUMN => $email]);
	}

	/**
	 * @param int $id
	 * @return Entity\User
	 */
	public function getUserById($id) {
		try {
			return $this->createQueryBuilder('e')
				->addSelect('r')
				->leftJoin('e.role', 'r')
				->where('e.id = :id')
				->setParameter('id', $id)
				->getQuery()
				->getSingleResult();
		} catch (NoResultException $e) {
			return NULL;
		}
	}

	/**
	 * @param string $email
	 * @return Entity\User
	 */
	public function getUserByEmail($email) {
		return $this->findOneBy(['email' => $email]);
	}

	/**
	 * @param string $hash
	 * @param int $id
	 * @param \DateTime $time
	 * @return \Entity\User
	 */
	public function getUserByForgotHash($hash, $id, \DateTime $time = NULL) {
		return $this->findOneBy([
			'forgotHash' => $hash, 'forgotTime' => $time ? : new \DateTime, 'id' => $id,
		]);
	}

	/**
	 * @param mixed $value
	 * @return Entity\User
	 */
	public function login($value) {
		try {
			return $this->createQueryBuilder('e')
				->select('e, r')
				->leftJoin('e.role', 'r')
				->where('e.' . self::COLUMN . ' = :id')
				->setParameter('id', $value)
				->getQuery()
				->getSingleResult();
		} catch (NoResultException $e) {
			return NULL;
		}
	}

}
