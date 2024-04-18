<?php

namespace GeographieBundle\Repository;

/**
 * VilleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VilleRepository extends \Doctrine\ORM\EntityRepository
{
	public function findByGeo($lat, $lon){
		return $this->createQueryBuilder("v")
			->where("v.latitude = :latitude")
			->andWhere("v.longitude = :longitude")
			->setParameter("latitude", $lat)
			->setParameter("longitude", $lon)
			->getQuery()->getResult();
	}

	public function findByName($name){
		return $this->createQueryBuilder("v")
			->where("v.slug = :name")
			->orWhere("v.nom = :name")
			->orWhere("v.locality = :name")
			->setParameter("name", $name)
			->getQuery()->getResult();
	}


	public function findByCodePostal($code){
		return $this->createQueryBuilder("v")
			->join("v.codePostaux", "cp")
			->where("cp.code = :code")
			->setParameter("code", $code)
			->getQuery()->getResult();
	}


	private function getVilleProche($lat, $lon, $radius){
		list($lon_min, $lat_min, $lon_max, $lat_max) = $this->bounding_box($lat, $lon, $radius);

		return $this->createQueryBuilder("v")
			->where("v.latitude < :minLat")
			->andWhere("v.latitude > :maxLat")
			->andWhere("v.longitude < :maxLon")
			->andWhere("v.longitude > :minLon")
			->setParameter("minLat", $lat_min)
			->setParameter("minLon", $lon_min)
			->setParameter("maxLat", $lat_max)
			->setParameter("maxLon", $lon_max)
			->getQuery()->getResult();

	}


	private function bounding_box($latitude_in_degrees, $longitude_in_degrees, $half_side_in_km){
		$radius  = 6371;
		
		$lat = deg2rad($latitude_in_degrees);
		$lon = deg2rad($longitude_in_degrees);
		
		$parallel_radius = $radius * cos($lat);
		
		$lat_min = $lat - $half_side_in_km/$radius;
		$lat_max = $lat + $alf_side_in_km/$radius;
		$lon_min = $lon - $half_side_in_km/$parallel_radius;
		$lon_max = $lon + $half_side_in_km/$parallel_radius;
		
		$lat_min = rad2deg($lat_min);
		$lon_min = rad2deg($lon_min);
		$lat_max = rad2deg($lat_max);
		$lon_max = rad2deg($lon_max);
		
		
		
		return Array($lon_min, $lat_min, $lon_max, $lat_max);}
}
