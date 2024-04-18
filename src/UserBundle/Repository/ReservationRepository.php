<?php

namespace UserBundle\Repository;

/**
 * ReservationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReservationRepository extends \Doctrine\ORM\EntityRepository
{
	public function getAll($order="asc"){
		return $this->createQueryBuilder("r")
			->orberBy("r;createdAt", 'asc')
			->getQuery()->getResult();
	}

	public function getEncours($order="asc", $value=true){
		return $this->createQueryBuilder("r")
			->where("r.encours = :val")
			->orberBy("r;createdAt", 'asc')
			->setParameter("val", $val)
			->getQuery()->getResult();
	}
}
